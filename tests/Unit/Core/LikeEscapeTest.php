<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Pipeline\SearchStep;
use Livewire\Tables\Core\State;
use Livewire\Tables\Filters\TextFilter;

beforeEach(function (): void {
    Schema::connection('testing')->create('like_items', function ($table): void {
        $table->id();
        $table->string('name');
        $table->string('code')->default('');
    });
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('like_items');
});

function makeLikeModel(): Model
{
    return new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'like_items';

        protected $guarded = [];

        public $timestamps = false;
    };
}

function makeLikeState(string $search): StateContract
{
    return new State(search: $search);
}

test('SearchStep escapes percent wildcard in search term', function (): void {
    makeLikeModel()->newQuery()->insert([
        ['name' => '100%', 'code' => 'a'],
        ['name' => '100 percent', 'code' => 'b'],
    ]);

    $columns = [
        TextColumn::make('name')->searchable(),
    ];

    $step = new SearchStep($columns);
    $query = makeLikeModel()->newQuery();
    $state = makeLikeState('100%');

    $result = $step->apply($query, $state);
    $rows = $result->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->name)->toBe('100%');
});

test('SearchStep escapes underscore wildcard in search term', function (): void {
    makeLikeModel()->newQuery()->insert([
        ['name' => 'test_value', 'code' => 'a'],
        ['name' => 'testXvalue', 'code' => 'b'],
    ]);

    $columns = [
        TextColumn::make('name')->searchable(),
    ];

    $step = new SearchStep($columns);
    $query = makeLikeModel()->newQuery();
    $state = makeLikeState('test_value');

    $result = $step->apply($query, $state);
    $rows = $result->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->name)->toBe('test_value');
});

test('TextFilter escapes percent wildcard in filter value', function (): void {
    makeLikeModel()->newQuery()->insert([
        ['name' => '100%', 'code' => 'a'],
        ['name' => '100 percent', 'code' => 'b'],
    ]);

    $filter = TextFilter::make('name');
    $builder = makeLikeModel()->newQuery();
    $result = $filter->apply($builder, '100%');
    $rows = $result->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->name)->toBe('100%');
});

test('TextFilter escapes underscore wildcard in filter value', function (): void {
    makeLikeModel()->newQuery()->insert([
        ['name' => 'test_value', 'code' => 'a'],
        ['name' => 'testXvalue', 'code' => 'b'],
    ]);

    $filter = TextFilter::make('name');
    $builder = makeLikeModel()->newQuery();
    $result = $filter->apply($builder, 'test_value');
    $rows = $result->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->name)->toBe('test_value');
});

test('SearchStep truncates search term to 200 characters', function (): void {
    $longSearch = str_repeat('a', 300);
    $state = makeLikeState($longSearch);

    $columns = [
        TextColumn::make('name')->searchable(),
    ];

    $step = new SearchStep($columns);
    $query = makeLikeModel()->newQuery();

    $sql = $step->apply($query, $state)->toSql();

    expect(strlen($sql))->toBeLessThan(600);
});

test('SearchStep sanitizes field name by stripping non-alphanumeric chars except dot and underscore', function (): void {
    $columns = [
        TextColumn::make('name')->searchable(),
    ];

    $step = new SearchStep($columns);
    $query = makeLikeModel()->newQuery();
    $state = makeLikeState('test');

    $sql = $step->apply($query, $state)->toSql();

    expect($sql)->not->toContain(';')
        ->and($sql)->not->toContain('DROP')
        ->and($sql)->not->toContain("'name'");
});

test('SearchStep passes already-truncated search term to custom search callback', function (): void {
    $receivedSearch = null;

    $column = TextColumn::make('name')
        ->searchable(function ($query, $search) use (&$receivedSearch) {
            $receivedSearch = $search;

            return $query;
        });

    $longSearch = str_repeat('x', 300);
    $step = new SearchStep([$column]);
    $query = makeLikeModel()->newQuery();
    $state = makeLikeState($longSearch);

    $step->apply($query, $state);

    expect($receivedSearch)->not->toBeNull()
        ->and(mb_strlen($receivedSearch))->toBeLessThanOrEqual(200);
});
