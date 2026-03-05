<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Tables\Core\Pipeline\FilterStep;
use Livewire\Tables\Core\State;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;

beforeEach(function (): void {
    Schema::connection('testing')->create('filter_step_items', function ($table): void {
        $table->id();
        $table->string('name')->nullable();
        $table->string('tier')->nullable();
    });
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('filter_step_items');
});

function makeFilterModel(): Model
{
    return new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'filter_step_items';

        protected $guarded = [];
    };
}

test('filter step applies filter when state uses auto-derived key for dotted field', function (): void {
    $applied = false;

    $filter = SelectFilter::make('brands.tier')
        ->filter(function ($query, $value) use (&$applied) {
            $applied = true;

            return $query->where('tier', $value);
        });

    $state = new State(
        search: '',
        sortFields: [],
        filters: ['brands_tier' => 'premium'],
        perPage: 10,
        page: 1,
    );

    $builder = makeFilterModel()->newQuery();
    $step = new FilterStep([$filter]);
    $step->apply($builder, $state);

    expect($applied)->toBeTrue();
});

test('filter step does not apply filter when state uses dotted key directly', function (): void {
    $applied = false;

    $filter = SelectFilter::make('brands.tier')
        ->filter(function ($query, $value) use (&$applied) {
            $applied = true;

            return $query->where('tier', $value);
        });

    $state = new State(
        search: '',
        sortFields: [],
        filters: ['brands.tier' => 'premium'],
        perPage: 10,
        page: 1,
    );

    $builder = makeFilterModel()->newQuery();
    $step = new FilterStep([$filter]);
    $step->apply($builder, $state);

    expect($applied)->toBeFalse();
});

test('filter step applies plain field filter by field name key', function (): void {
    $applied = false;

    $filter = TextFilter::make('name')
        ->filter(function ($query, $value) use (&$applied) {
            $applied = true;

            return $query->where('name', $value);
        });

    $state = new State(
        search: '',
        sortFields: [],
        filters: ['name' => 'Acme'],
        perPage: 10,
        page: 1,
    );

    $builder = makeFilterModel()->newQuery();
    $step = new FilterStep([$filter]);
    $step->apply($builder, $state);

    expect($applied)->toBeTrue();
});

test('filter step skips empty filter values', function (): void {
    $applied = false;

    $filter = SelectFilter::make('brands.tier')
        ->filter(function ($query, $value) use (&$applied) {
            $applied = true;

            return $query->where('tier', $value);
        });

    $state = new State(
        search: '',
        sortFields: [],
        filters: ['brands_tier' => ''],
        perPage: 10,
        page: 1,
    );

    $builder = makeFilterModel()->newQuery();
    $step = new FilterStep([$filter]);
    $step->apply($builder, $state);

    expect($applied)->toBeFalse();
});
