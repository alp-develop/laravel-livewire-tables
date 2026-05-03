<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class FilterUpdateModel extends Model
{
    protected $connection = 'testing';

    protected $table = 'filter_update_items';

    protected $guarded = [];

    public $timestamps = false;
}

class FilterUpdateTable extends DataTableComponent
{
    public string $tableKey = 'filter-update-test';

    public function query(): Builder
    {
        return FilterUpdateModel::query();
    }

    public function columns(): array
    {
        return [
            Column::text('name')->searchable()->sortable(),
            Column::text('status'),
        ];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('name')
                ->filter(fn ($q, $v) => $q->where('name', 'like', "%{$v}%")),
            SelectFilter::make('status')
                ->setOptions(['active' => 'Active', 'inactive' => 'Inactive'])
                ->filter(fn ($q, $v) => $q->where('status', $v)),
        ];
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function configure(): void {}
}

class DependentFilterTable extends DataTableComponent
{
    public string $tableKey = 'dependent-filter-test';

    public function query(): Builder
    {
        return FilterUpdateModel::query();
    }

    public function columns(): array
    {
        return [
            Column::text('name'),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('status')
                ->setOptions(['active' => 'Active', 'inactive' => 'Inactive'])
                ->filter(fn ($q, $v) => $q->where('status', $v)),
            SelectFilter::make('sub_status')
                ->parent('status')
                ->filter(fn ($q, $v) => $q->where('status', $v)),
        ];
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function configure(): void {}
}

beforeEach(function (): void {
    Schema::connection('testing')->create('filter_update_items', function ($table): void {
        $table->id();
        $table->string('name');
        $table->string('status');
    });

    FilterUpdateModel::insert([
        ['name' => 'Alpha', 'status' => 'active'],
        ['name' => 'Beta', 'status' => 'inactive'],
        ['name' => 'Gamma', 'status' => 'active'],
    ]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('filter_update_items');
});

test('setting tableFilters resets deselectAll and dispatches event', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->call('toggleSelected', '1');
    expect($component->instance()->selectedIds)->toContain('1');

    $component->set('tableFilters.status', 'active');
    expect($component->instance()->selectedIds)->toBe([]);
});

test('setting tableFilters with valid select value normalizes value', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->set('tableFilters.status', 'active');
    expect($component->instance()->getFilterValue('status'))->toBe('active');
});

test('setting tableFilters to empty string normalizes select to empty', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->set('tableFilters.status', 'active');
    $component->set('tableFilters.status', '');
    expect($component->instance()->getFilterValue('status'))->toBe('');
});

test('hasActiveFilters returns true after setting filter value', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->set('tableFilters.status', 'active');
    expect($component->instance()->hasActiveFilters())->toBeTrue();
});

test('hasActiveFilters returns false after clearing filter', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->set('tableFilters.status', 'active');
    $component->call('clearFilters');
    expect($component->instance()->hasActiveFilters())->toBeFalse();
});

test('updatedTableFilters clears dependent child filter when parent changes', function (): void {
    $component = Livewire::test(DependentFilterTable::class);

    $component->set('tableFilters.sub_status', 'active');
    expect($component->instance()->getFilterValue('sub_status'))->toBe('active');

    $component->set('tableFilters.status', 'inactive');
    expect($component->instance()->getFilterValue('sub_status'))->toBe('');
});

test('clearFilters resets all filter values and fires event', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->set('tableFilters.name', 'Alpha');
    $component->set('tableFilters.status', 'active');

    $component->call('clearFilters');

    expect($component->instance()->getFilterValue('name'))->toBe('');
    expect($component->instance()->getFilterValue('status'))->toBe('');
    expect($component->instance()->hasActiveFilters())->toBeFalse();
});

test('applyFilter sets value and resets selection', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->call('toggleSelected', '1');
    $component->call('applyFilter', 'status', 'active');

    expect($component->instance()->getFilterValue('status'))->toBe('active');
    expect($component->instance()->selectedIds)->toBe([]);
});

test('removeFilter resets single filter value', function (): void {
    $component = Livewire::test(FilterUpdateTable::class);

    $component->set('tableFilters.status', 'active');
    $component->call('removeFilter', 'status');

    expect($component->instance()->getFilterValue('status'))->toBe('');
});
