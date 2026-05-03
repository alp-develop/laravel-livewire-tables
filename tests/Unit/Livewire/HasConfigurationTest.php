<?php

declare(strict_types=1);

use Livewire\Tables\Livewire\Concerns\HasConfiguration;
use Livewire\Tables\Themes\ThemeManager;

function makeConfigActor(): object
{
    return new class
    {
        use HasConfiguration;

        public string $tableTheme = 'tailwind';

        public function callSet(string $method, mixed ...$args): static
        {
            return $this->{$method}(...$args);
        }
    };
}

test('setDefaultSortDirection accepts desc without throwing', function (): void {
    $actor = makeConfigActor();
    expect(fn () => $actor->callSet('setDefaultSortDirection', 'desc'))->not->toThrow(Throwable::class);
});

test('setDefaultSortDirection falls back to asc for invalid value without throwing', function (): void {
    $actor = makeConfigActor();
    expect(fn () => $actor->callSet('setDefaultSortDirection', 'invalid'))->not->toThrow(Throwable::class);
});

test('setEmptyMessage stores and returns custom message', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setEmptyMessage', 'Nothing here');
    expect($actor->getEmptyMessage())->toBe('Nothing here');
});

test('getEmptyMessage returns translation key when no custom message', function (): void {
    $actor = makeConfigActor();
    expect($actor->getEmptyMessage())->toBeString()->not->toBe('');
});

test('setHeadClass and getHeadClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setHeadClass', 'custom-head');
    expect($actor->getHeadClass())->toBe('custom-head');
});

test('setBodyClass and getBodyClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setBodyClass', 'custom-body');
    expect($actor->getBodyClass())->toBe('custom-body');
});

test('setRowClass with string and resolveRowClass returns it', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setRowClass', 'row-highlight');
    expect($actor->resolveRowClass(new stdClass))->toBe('row-highlight');
});

test('setRowClass with closure returns closure result', function (): void {
    $actor = makeConfigActor();
    $row = new stdClass;
    $row->active = true;
    $actor->callSet('setRowClass', fn ($r) => $r->active ? 'bg-green' : 'bg-red');
    expect($actor->resolveRowClass($row))->toBe('bg-green');
});

test('resolveRowClass returns empty string when not set', function (): void {
    $actor = makeConfigActor();
    expect($actor->resolveRowClass(new stdClass))->toBe('');
});

test('setFilterGroupClass and getFilterGroupClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setFilterGroupClass', 'filter-group-custom');
    expect($actor->getFilterGroupClass())->toBe('filter-group-custom');
});

test('setFilterLabelClass and getFilterLabelClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setFilterLabelClass', 'label-custom');
    expect($actor->getFilterLabelClass())->toBe('label-custom');
});

test('setFilterInputClass and getFilterInputClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setFilterInputClass', 'input-custom');
    expect($actor->getFilterInputClass())->toBe('input-custom');
});

test('setFilterBtnClass and getFilterBtnClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setFilterBtnClass', 'btn-filter');
    expect($actor->getFilterBtnClass())->toBe('btn-filter');
});

test('setFilterBtnActiveClass and getFilterBtnActiveClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setFilterBtnActiveClass', 'btn-filter-active');
    expect($actor->getFilterBtnActiveClass())->toBe('btn-filter-active');
});

test('setColumnBtnClass and getColumnBtnClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setColumnBtnClass', 'btn-col');
    expect($actor->getColumnBtnClass())->toBe('btn-col');
});

test('setBulkBtnClass and getBulkBtnClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setBulkBtnClass', 'btn-bulk');
    expect($actor->getBulkBtnClass())->toBe('btn-bulk');
});

test('setBulkBtnActiveClass and getBulkBtnActiveClass roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setBulkBtnActiveClass', 'btn-bulk-active');
    expect($actor->getBulkBtnActiveClass())->toBe('btn-bulk-active');
});

test('setSearchDebounce clamps to 0-5000 range', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setSearchDebounce', -100);
    expect($actor->getSearchDebounce())->toBe(0);
    $actor->callSet('setSearchDebounce', 9999);
    expect($actor->getSearchDebounce())->toBe(5000);
    $actor->callSet('setSearchDebounce', 500);
    expect($actor->getSearchDebounce())->toBe(500);
});

test('setEagerLoad and getEagerLoad roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setEagerLoad', ['author', 'tags']);
    expect($actor->getEagerLoad())->toBe(['author', 'tags']);
});

test('setPerPageOptions and getPerPageOptions roundtrip', function (): void {
    $actor = makeConfigActor();
    $actor->callSet('setPerPageOptions', [5, 10, 20]);
    expect($actor->getPerPageOptions())->toBe([5, 10, 20]);
});

test('isTailwind returns true for tailwind theme', function (): void {
    $actor = makeConfigActor();
    app(ThemeManager::class)->use('tailwind');
    expect($actor->isTailwind())->toBeTrue();
    expect($actor->isBootstrap())->toBeFalse();
});

test('isBootstrap5 returns true for bootstrap5 theme', function (): void {
    $actor = new class
    {
        use HasConfiguration;

        public string $tableTheme = 'bootstrap5';

        public function callSet(string $method, mixed ...$args): static
        {
            return $this->{$method}(...$args);
        }
    };
    app(ThemeManager::class)->use('bootstrap5');
    expect($actor->isBootstrap5())->toBeTrue();
    expect($actor->isBootstrap4())->toBeFalse();
    expect($actor->isBootstrap())->toBeTrue();
});
