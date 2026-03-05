<?php

declare(strict_types=1);

use Livewire\Tables\Livewire\Concerns\HasBulkActions;

function makeBulkActor(): object
{
    return new class
    {
        use HasBulkActions;

        public function bulkActions(): array
        {
            return [];
        }
    };
}

function makeBulkActorWithActions(): object
{
    return new class
    {
        use HasBulkActions;

        public function bulkActions(): array
        {
            return ['delete' => 'Delete', 'export' => 'Export'];
        }
    };
}

test('selectedIds is empty by default', function (): void {
    expect(makeBulkActor()->selectedIds)->toBe([]);
});

test('excludedIds is empty by default', function (): void {
    expect(makeBulkActor()->excludedIds)->toBe([]);
});

test('selectAllPages is false by default', function (): void {
    expect(makeBulkActor()->selectAllPages)->toBeFalse();
});

test('pageIds is empty by default', function (): void {
    expect(makeBulkActor()->pageIds)->toBe([]);
});

test('hasBulkActions returns false when bulkActions is empty', function (): void {
    expect(makeBulkActor()->hasBulkActions())->toBeFalse();
});

test('hasBulkActions returns true when bulkActions has entries', function (): void {
    expect(makeBulkActorWithActions()->hasBulkActions())->toBeTrue();
});

test('toggleSelected adds an id when selectAllPages is false', function (): void {
    $actor = makeBulkActor();
    $actor->toggleSelected(1);

    expect($actor->selectedIds)->toBe(['1'])
        ->and($actor->excludedIds)->toBe([]);
});

test('toggleSelected removes an already selected id when selectAllPages is false', function (): void {
    $actor = makeBulkActor();
    $actor->toggleSelected(1);
    $actor->toggleSelected(1);

    expect($actor->selectedIds)->toBe([]);
});

test('toggleSelected casts id to string', function (): void {
    $actor = makeBulkActor();
    $actor->toggleSelected(42);

    expect($actor->selectedIds[0])->toBeString()->toBe('42');
});

test('toggleSelected adds to excludedIds when selectAllPages is true', function (): void {
    $actor = makeBulkActor();
    $actor->selectAllAcrossPages();
    $actor->toggleSelected(5);

    expect($actor->excludedIds)->toBe(['5'])
        ->and($actor->selectedIds)->toBe([]);
});

test('toggleSelected removes from excludedIds when re-checked in selectAllPages mode', function (): void {
    $actor = makeBulkActor();
    $actor->selectAllAcrossPages();
    $actor->toggleSelected(5);
    $actor->toggleSelected(5);

    expect($actor->excludedIds)->toBe([]);
});

test('setPageSelection selects ids when selectAllPages is false', function (): void {
    $actor = makeBulkActor();
    $actor->setPageSelection([1, 2, 3], true);

    expect($actor->selectedIds)->toBe(['1', '2', '3']);
});

test('setPageSelection deselects ids when selectAllPages is false', function (): void {
    $actor = makeBulkActor();
    $actor->setPageSelection([1, 2, 3], true);
    $actor->setPageSelection([2], false);

    expect($actor->selectedIds)->toBe(['1', '3']);
});

test('setPageSelection deduplicates ids when selecting', function (): void {
    $actor = makeBulkActor();
    $actor->setPageSelection([1, 2], true);
    $actor->setPageSelection([2, 3], true);

    expect($actor->selectedIds)->toBe(['1', '2', '3']);
});

test('setPageSelection in selectAllPages mode removes page ids from excludedIds when selecting', function (): void {
    $actor = makeBulkActor();
    $actor->selectAllAcrossPages();
    $actor->toggleSelected(1);
    $actor->toggleSelected(2);
    $actor->setPageSelection([1, 2], true);

    expect($actor->excludedIds)->toBe([]);
});

test('setPageSelection in selectAllPages mode adds page ids to excludedIds when deselecting', function (): void {
    $actor = makeBulkActor();
    $actor->selectAllAcrossPages();
    $actor->setPageSelection([1, 2, 3], false);

    expect($actor->excludedIds)->toBe(['1', '2', '3']);
});

test('selectAllAcrossPages sets flag and clears selectedIds and excludedIds', function (): void {
    $actor = makeBulkActor();
    $actor->setPageSelection([1, 2, 3], true);
    $actor->selectAllAcrossPages();

    expect($actor->selectAllPages)->toBeTrue()
        ->and($actor->selectedIds)->toBe([])
        ->and($actor->excludedIds)->toBe([]);
});

test('deselectAll clears selectedIds excludedIds and resets selectAllPages', function (): void {
    $actor = makeBulkActor();
    $actor->setPageSelection([1, 2], true);
    $actor->selectAllAcrossPages();
    $actor->toggleSelected(3);
    $actor->deselectAll();

    expect($actor->selectedIds)->toBe([])
        ->and($actor->excludedIds)->toBe([])
        ->and($actor->selectAllPages)->toBeFalse();
});

test('getSelectedCount returns count of selectedIds when selectAllPages is false', function (): void {
    $actor = makeBulkActor();
    $actor->setPageSelection([1, 2, 3], true);

    expect($actor->getSelectedCount(1000))->toBe(3);
});

test('getSelectedCount returns total minus excluded when selectAllPages is true', function (): void {
    $actor = makeBulkActor();
    $actor->selectAllAcrossPages();
    $actor->toggleSelected(1);
    $actor->toggleSelected(2);

    expect($actor->getSelectedCount(1000))->toBe(998);
});

test('getSelectedCount returns total when selectAllPages is true with no exclusions', function (): void {
    $actor = makeBulkActor();
    $actor->selectAllAcrossPages();

    expect($actor->getSelectedCount(500))->toBe(500);
});
