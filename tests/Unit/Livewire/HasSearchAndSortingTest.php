<?php

declare(strict_types=1);

use Livewire\Tables\Columns\Column;
use Livewire\Tables\Livewire\Concerns\HasSearch;
use Livewire\Tables\Livewire\Concerns\HasSorting;

function makeSearchActor(): object
{
    return new class
    {
        use HasSearch;

        public string $tableKey = 'test';

        public array $tableFilters = [];

        public int $deselectAllCalled = 0;

        public int $resetPageCalled = 0;

        public array $dispatched = [];

        public function deselectAll(): void
        {
            $this->deselectAllCalled++;
        }

        public function resetPage(): void
        {
            $this->resetPageCalled++;
        }

        public function dispatchFiltersChanged(): void
        {
            $this->dispatched[] = 'filters-changed';
        }

        public function getAppliedFilters(): array
        {
            return [];
        }
    };
}

function makeSortingActor(array $columns): object
{
    return new class($columns)
    {
        use HasSorting;

        public string $defaultSortDirection = 'asc';

        private array $columnDefs;

        public int $resetPageCalled = 0;

        public function __construct(array $columns)
        {
            $this->columnDefs = $columns;
        }

        public function resolveColumns(): array
        {
            return $this->columnDefs;
        }

        public function resetPage(): void
        {
            $this->resetPageCalled++;
        }
    };
}

test('hasSearch returns false when search is empty', function (): void {
    $actor = makeSearchActor();
    expect($actor->hasSearch())->toBeFalse();
});

test('hasSearch returns true when search has value', function (): void {
    $actor = makeSearchActor();
    $actor->search = 'alice';
    expect($actor->hasSearch())->toBeTrue();
});

test('hasSearch returns false for whitespace-only string', function (): void {
    $actor = makeSearchActor();
    $actor->search = '   ';
    expect($actor->hasSearch())->toBeFalse();
});

test('clearSearch resets search and calls side effects', function (): void {
    $actor = makeSearchActor();
    $actor->search = 'alice';
    $actor->clearSearch();
    expect($actor->search)->toBe('');
    expect($actor->deselectAllCalled)->toBe(1);
    expect($actor->resetPageCalled)->toBe(1);
    expect($actor->dispatched)->toContain('filters-changed');
});

test('updatedSearch truncates to 200 chars and calls side effects', function (): void {
    $actor = makeSearchActor();
    $actor->search = str_repeat('a', 300);
    $actor->updatedSearch();
    expect(strlen($actor->search))->toBe(200);
    expect($actor->deselectAllCalled)->toBe(1);
    expect($actor->resetPageCalled)->toBe(1);
});

test('updatedSearch preserves search under 200 chars', function (): void {
    $actor = makeSearchActor();
    $actor->search = 'alice';
    $actor->updatedSearch();
    expect($actor->search)->toBe('alice');
});

test('sortBy ignores non-sortable field', function (): void {
    $actor = makeSortingActor([
        Column::text('name')->sortable(),
        Column::text('email'),
    ]);
    $actor->sortBy('email');
    expect($actor->sortFields)->toBe([]);
});

test('sortBy adds sortable field with default direction', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    $actor->sortBy('name');
    expect($actor->sortFields)->toBe(['name' => 'asc']);
});

test('sortBy toggles asc to desc on second call', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    $actor->sortBy('name');
    $actor->sortBy('name');
    expect($actor->sortFields)->toBe(['name' => 'desc']);
});

test('sortBy removes field on third call', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    $actor->sortBy('name');
    $actor->sortBy('name');
    $actor->sortBy('name');
    expect($actor->sortFields)->toBe([]);
});

test('clearSort resets all sort fields', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable(), Column::text('email')->sortable()]);
    $actor->sortBy('name');
    $actor->sortBy('email');
    $actor->clearSort();
    expect($actor->sortFields)->toBe([]);
    expect($actor->resetPageCalled)->toBeGreaterThan(0);
});

test('clearSortField removes only specified field', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable(), Column::text('email')->sortable()]);
    $actor->sortBy('name');
    $actor->sortBy('email');
    $actor->clearSortField('name');
    expect($actor->sortFields)->toHaveKey('email');
    expect($actor->sortFields)->not->toHaveKey('name');
});

test('isSortedBy returns false when field not sorted', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    expect($actor->isSortedBy('name'))->toBeFalse();
});

test('isSortedBy returns true after sortBy called', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    $actor->sortBy('name');
    expect($actor->isSortedBy('name'))->toBeTrue();
});

test('getSortDirection returns asc for unsorted field', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    expect($actor->getSortDirection('name'))->toBe('asc');
});

test('getSortDirection returns current direction', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    $actor->sortBy('name');
    $actor->sortBy('name');
    expect($actor->getSortDirection('name'))->toBe('desc');
});

test('getSortOrder returns 0 for unsorted field', function (): void {
    $actor = makeSortingActor([Column::text('name')->sortable()]);
    expect($actor->getSortOrder('name'))->toBe(0);
});

test('getSortOrder returns 1-based position', function (): void {
    $actor = makeSortingActor([
        Column::text('name')->sortable(),
        Column::text('email')->sortable(),
    ]);
    $actor->sortBy('name');
    $actor->sortBy('email');
    expect($actor->getSortOrder('name'))->toBe(1);
    expect($actor->getSortOrder('email'))->toBe(2);
});
