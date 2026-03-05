<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Tables\Core\Pipeline\FilterStep;
use Livewire\Tables\Core\State;
use Livewire\Tables\Filters\DateFilter;
use Livewire\Tables\Filters\DateRangeFilter;
use Livewire\Tables\Filters\MultiDateFilter;
use Livewire\Tables\Filters\NumberFilter;
use Livewire\Tables\Filters\NumberRangeFilter;
use Livewire\Tables\Filters\SelectFilter;

beforeEach(function (): void {
    Schema::connection('testing')->create('clamp_items', function ($table): void {
        $table->id();
        $table->float('price')->nullable();
        $table->date('released_at')->nullable();
    });
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('clamp_items');
});

function makeClampModel(): Model
{
    return new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'clamp_items';

        protected $guarded = [];
    };
}

test('NumberFilter clamps value below min to min bound', function (): void {
    $applied = null;

    $filter = NumberFilter::make('price')
        ->min(0.0)
        ->max(100.0)
        ->filter(function ($query, $value) use (&$applied) {
            $applied = $value;

            return $query;
        });

    $state = new State(filters: ['price' => '-999']);
    $builder = makeClampModel()->newQuery();
    $step = new FilterStep([$filter]);
    $step->apply($builder, $state);

    expect($applied)->toBe(0.0);
});

test('NumberFilter apply() clamps negative to min 0', function (): void {
    $filter = NumberFilter::make('price')->min(0.0)->max(100.0);
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, '-50');
    $sql = $result->toSql();

    expect($sql)->toContain('where');
    $bindings = $result->getBindings();
    expect($bindings[0])->toBe(0.0);
});

test('NumberFilter apply() clamps value above max to max', function (): void {
    $filter = NumberFilter::make('price')->min(0.0)->max(100.0);
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, '9999');
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe(100.0);
});

test('NumberFilter apply() passes value through when within bounds', function (): void {
    $filter = NumberFilter::make('price')->min(0.0)->max(100.0);
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, '50');
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe(50.0);
});

test('NumberRangeFilter apply() clamps min input below minBound', function (): void {
    $filter = NumberRangeFilter::make('price')->min(0.0)->max(500.0);
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, ['min' => '-100', 'max' => '']);
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe(0.0);
});

test('NumberRangeFilter apply() clamps max input above maxBound', function (): void {
    $filter = NumberRangeFilter::make('price')->min(0.0)->max(500.0);
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, ['min' => '', 'max' => '99999']);
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe(500.0);
});

test('DateFilter apply() clamps date below minDate to minDate', function (): void {
    $filter = DateFilter::make('released_at')->minDate('2020-01-01')->maxDate('2027-12-31');
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, '2010-06-15');
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe('2020-01-01');
});

test('DateFilter apply() clamps date above maxDate to maxDate', function (): void {
    $filter = DateFilter::make('released_at')->minDate('2020-01-01')->maxDate('2027-12-31');
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, '2099-01-01');
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe('2027-12-31');
});

test('DateFilter apply() passes date through when within bounds', function (): void {
    $filter = DateFilter::make('released_at')->minDate('2020-01-01')->maxDate('2027-12-31');
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, '2023-06-15');
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe('2023-06-15');
});

test('DateRangeFilter apply() clamps from date below minDate', function (): void {
    $filter = DateRangeFilter::make('released_at')->minDate('2020-01-01')->maxDate('2027-12-31');
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, ['from' => '2010-03-01', 'to' => '']);
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe('2020-01-01');
});

test('DateRangeFilter apply() clamps to date above maxDate', function (): void {
    $filter = DateRangeFilter::make('released_at')->minDate('2020-01-01')->maxDate('2027-12-31');
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, ['from' => '', 'to' => '2099-12-31']);
    $bindings = $result->getBindings();

    expect($bindings[0])->toBe('2027-12-31');
});

test('MultiDateFilter apply() uses orWhereDate for each date', function (): void {
    $filter = MultiDateFilter::make('released_at');
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, ['2023-01-01', '2024-06-15']);
    $sql = $result->toSql();
    $bindings = $result->getBindings();

    expect($sql)->toContain('where')
        ->and($bindings)->toContain('2023-01-01')
        ->and($bindings)->toContain('2024-06-15');
});

test('MultiDateFilter apply() returns query unchanged for empty array', function (): void {
    $filter = MultiDateFilter::make('released_at');
    $builder = makeClampModel()->newQuery();
    $original = $builder->toSql();

    $result = $filter->apply($builder, []);

    expect($result->toSql())->toBe($original);
});

test('SelectFilter multiple apply() uses whereIn', function (): void {
    $filter = SelectFilter::make('price')->multiple()
        ->setOptions(['10' => 'Ten', '20' => 'Twenty']);
    $builder = makeClampModel()->newQuery();

    $result = $filter->apply($builder, ['10', '20']);
    $sql = $result->toSql();
    $bindings = $result->getBindings();

    expect($sql)->toContain('in')
        ->and($bindings)->toContain('10')
        ->and($bindings)->toContain('20');
});

test('SelectFilter multiple apply() returns query unchanged for empty array', function (): void {
    $filter = SelectFilter::make('price')->multiple();
    $builder = makeClampModel()->newQuery();
    $original = $builder->toSql();

    $result = $filter->apply($builder, []);

    expect($result->toSql())->toBe($original);
});
