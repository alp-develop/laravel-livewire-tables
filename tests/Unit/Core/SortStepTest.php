<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Core\Pipeline\SortStep;
use Livewire\Tables\Core\State;

beforeEach(function (): void {
    Schema::connection('testing')->create('sort_step_items', function ($table): void {
        $table->id();
        $table->string('name')->nullable();
        $table->integer('score')->default(0);
    });
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('sort_step_items');
});

function makeSortModel(): Model
{
    return new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'sort_step_items';

        protected $guarded = [];

        public $timestamps = false;
    };
}

test('SortStep rejects sort field not in sortable columns whitelist', function (): void {
    $columns = [
        Column::text('name')->sortable(),
    ];

    $state = new State(
        search: '',
        sortFields: ['nonexistent_column' => 'asc', 'injected; DROP TABLE users' => 'asc'],
        filters: [],
        perPage: 10,
        page: 1,
    );

    $step = new SortStep($columns);
    $query = makeSortModel()->newQuery();

    $sql = $step->apply($query, $state)->toSql();

    expect($sql)->not->toContain('nonexistent_column')
        ->and($sql)->not->toContain('DROP')
        ->and($sql)->not->toContain('injected');
});

test('SortStep rejects field names containing non-alphanumeric chars', function (): void {
    $evilColumn = Column::text('name; DROP TABLE users')->sortable();

    $state = new State(
        search: '',
        sortFields: ['name; DROP TABLE users' => 'asc'],
        filters: [],
        perPage: 10,
        page: 1,
    );

    $step = new SortStep([$evilColumn]);
    $query = makeSortModel()->newQuery();

    $sql = $step->apply($query, $state)->toSql();

    expect($sql)->not->toContain('; DROP')
        ->and($sql)->not->toContain("'users'")
        ->and($sql)->not->toContain('order by');
});

test('SortStep normalizes unknown direction to asc', function (): void {
    $columns = [
        Column::text('name')->sortable(),
    ];

    $state = new State(
        search: '',
        sortFields: ['name' => 'HACKED_DIRECTION'],
        filters: [],
        perPage: 10,
        page: 1,
    );

    $step = new SortStep($columns);
    $query = makeSortModel()->newQuery();

    $sql = $step->apply($query, $state)->toSql();

    expect($sql)->toContain('asc')
        ->and($sql)->not->toContain('HACKED_DIRECTION');
});

test('SortStep applies valid sort field correctly', function (): void {
    makeSortModel()->newQuery()->insert([
        ['name' => 'Zebra', 'score' => 1],
        ['name' => 'Alpha', 'score' => 2],
    ]);

    $columns = [
        Column::text('name')->sortable(),
    ];

    $state = new State(
        search: '',
        sortFields: ['name' => 'asc'],
        filters: [],
        perPage: 10,
        page: 1,
    );

    $step = new SortStep($columns);
    $query = makeSortModel()->newQuery();

    $rows = $step->apply($query, $state)->get();

    expect($rows->first()->name)->toBe('Alpha')
        ->and($rows->last()->name)->toBe('Zebra');
});
