<?php

declare(strict_types=1);

use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Filters\DateRangeFilter;
use Livewire\Tables\Filters\MultiDateFilter;
use Livewire\Tables\Filters\NumberRangeFilter;
use Livewire\Tables\Livewire\Concerns\HasFilters;

function makeFiltersActor(array $filters): object
{
    return new class ($filters)
    {
        use HasFilters;

        public string $tableKey = 'test-table';
        public string $search = '';
        private array $filterDefinitions;
        public array $dispatched = [];
        public int $deselectAllCalled = 0;
        public int $resetPageCalled = 0;

        public function __construct(array $filters)
        {
            $this->filterDefinitions = $filters;
        }

        public function filters(): array
        {
            return $this->filterDefinitions;
        }

        public function deselectAll(): void
        {
            ++$this->deselectAllCalled;
        }

        public function resetPage(): void
        {
            ++$this->resetPageCalled;
        }

        public function dispatch(string $event, mixed ...$args): void
        {
            $this->dispatched[] = $event;
        }

        public function getAppliedFilters(): array
        {
            return $this->tableFilters;
        }

        public function callFilterHasActiveValue(string $key): bool
        {
            return $this->filterHasActiveValue($key);
        }
    };
}

test('getFilterValue returns null when key not present', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    expect($actor->getFilterValue('name'))->toBeNull();
});

test('getFilterValue returns stored value', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->tableFilters['name'] = 'Alice';
    expect($actor->getFilterValue('name'))->toBe('Alice');
});

test('filterHasActiveValue returns false when key missing', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    expect($actor->callFilterHasActiveValue('name'))->toBeFalse();
});

test('filterHasActiveValue returns false for empty string', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->tableFilters['name'] = '';
    expect($actor->callFilterHasActiveValue('name'))->toBeFalse();
});

test('filterHasActiveValue returns true for non-empty string', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->tableFilters['name'] = 'Bob';
    expect($actor->callFilterHasActiveValue('name'))->toBeTrue();
});

test('filterHasActiveValue returns false for empty array', function (): void {
    $actor = makeFiltersActor([SelectFilter::make('role')]);
    $actor->tableFilters['role'] = [];
    expect($actor->callFilterHasActiveValue('role'))->toBeFalse();
});

test('filterHasActiveValue returns true for non-empty array', function (): void {
    $actor = makeFiltersActor([SelectFilter::make('role')]);
    $actor->tableFilters['role'] = ['admin'];
    expect($actor->callFilterHasActiveValue('role'))->toBeTrue();
});

test('hasActiveFilters returns false when all filters empty', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->tableFilters = ['name' => ''];
    expect($actor->hasActiveFilters())->toBeFalse();
});

test('hasActiveFilters returns true when any filter has value', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name'), TextFilter::make('email')]);
    $actor->tableFilters = ['name' => '', 'email' => 'foo@bar.com'];
    expect($actor->hasActiveFilters())->toBeTrue();
});

test('applyFilter ignores unknown keys', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->applyFilter('unknown', 'test');
    expect($actor->tableFilters)->toBe([]);
    expect($actor->dispatched)->toBe([]);
});

test('applyFilter sets value for valid key and calls side effects', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->applyFilter('name', 'Alice');
    expect($actor->getFilterValue('name'))->toBe('Alice');
    expect($actor->deselectAllCalled)->toBe(1);
    expect($actor->resetPageCalled)->toBe(1);
    expect($actor->dispatched)->toContain('table-filters-applied');
});

test('removeFilter ignores unknown keys', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->removeFilter('unknown');
    expect($actor->dispatched)->toBe([]);
    expect($actor->deselectAllCalled)->toBe(0);
});

test('removeFilter resets text filter to empty string', function (): void {
    $actor = makeFiltersActor([TextFilter::make('name')]);
    $actor->tableFilters['name'] = 'Alice';
    $actor->removeFilter('name');
    expect($actor->getFilterValue('name'))->toBe('');
    expect($actor->deselectAllCalled)->toBe(1);
    expect($actor->resetPageCalled)->toBe(1);
    expect($actor->dispatched)->toContain('remove-filter');
    expect($actor->dispatched)->toContain('table-filters-applied');
});

test('removeFilter resets date_range filter to array with from and to', function (): void {
    $actor = makeFiltersActor([DateRangeFilter::make('created_at')]);
    $actor->tableFilters['created_at'] = ['from' => '2024-01-01', 'to' => '2024-12-31'];
    $actor->removeFilter('created_at');
    expect($actor->getFilterValue('created_at'))->toBe(['from' => '', 'to' => '']);
});

test('removeFilter resets number_range filter to array with min and max', function (): void {
    $actor = makeFiltersActor([NumberRangeFilter::make('price')]);
    $actor->tableFilters['price'] = ['min' => '10', 'max' => '100'];
    $actor->removeFilter('price');
    expect($actor->getFilterValue('price'))->toBe(['min' => '', 'max' => '']);
});

test('removeFilter resets multi_date filter to empty array', function (): void {
    $actor = makeFiltersActor([MultiDateFilter::make('dates')]);
    $actor->tableFilters['dates'] = ['2024-01-01'];
    $actor->removeFilter('dates');
    expect($actor->getFilterValue('dates'))->toBe([]);
});

test('removeFilter cascades to dependent SelectFilters', function (): void {
    $parent = SelectFilter::make('country');
    $child = SelectFilter::make('city')->parent('country');

    $actor = makeFiltersActor([$parent, $child]);
    $actor->tableFilters['country'] = 'es';
    $actor->tableFilters['city'] = 'madrid';

    $actor->removeFilter('country');
    expect($actor->getFilterValue('country'))->toBe('');
    expect($actor->getFilterValue('city'))->toBe('');
});

test('clearFilters resets all filters and dispatches clear event', function (): void {
    $actor = makeFiltersActor([
        TextFilter::make('name'),
        SelectFilter::make('status'),
    ]);
    $actor->tableFilters = ['name' => 'Alice', 'status' => 'active'];

    $actor->clearFilters();

    expect($actor->getFilterValue('name'))->toBe('');
    expect($actor->getFilterValue('status'))->toBe('');
    expect($actor->deselectAllCalled)->toBe(1);
    expect($actor->resetPageCalled)->toBe(1);
    expect($actor->dispatched)->toContain('livewire-tables:clear-filters');
    expect($actor->dispatched)->toContain('table-filters-applied');
});

test('clearFilters resets date_range and number_range to structured arrays', function (): void {
    $actor = makeFiltersActor([
        DateRangeFilter::make('created_at'),
        NumberRangeFilter::make('price'),
    ]);
    $actor->tableFilters = [
        'created_at' => ['from' => '2024-01-01', 'to' => '2024-12-31'],
        'price' => ['min' => '10', 'max' => '100'],
    ];

    $actor->clearFilters();

    expect($actor->getFilterValue('created_at'))->toBe(['from' => '', 'to' => '']);
    expect($actor->getFilterValue('price'))->toBe(['min' => '', 'max' => '']);
});
