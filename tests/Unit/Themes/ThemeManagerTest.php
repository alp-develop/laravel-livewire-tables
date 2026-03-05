<?php

declare(strict_types=1);

use Livewire\Tables\Core\Contracts\ThemeContract;
use Livewire\Tables\Themes\Bootstrap4Theme;
use Livewire\Tables\Themes\Bootstrap5Theme;
use Livewire\Tables\Themes\TailwindTheme;
use Livewire\Tables\Themes\ThemeManager;

test('theme manager resolves tailwind by default', function (): void {
    $manager = new ThemeManager;

    $theme = $manager->resolve();

    expect($theme)->toBeInstanceOf(TailwindTheme::class)
        ->and($theme->name())->toBe('tailwind');
});

test('theme manager can switch to bootstrap5', function (): void {
    $manager = new ThemeManager;
    $manager->use('bootstrap5');

    $theme = $manager->resolve();

    expect($theme)->toBeInstanceOf(Bootstrap5Theme::class)
        ->and($theme->name())->toBe('bootstrap5');
});

test('theme manager backward compat bootstrap alias resolves to bootstrap5', function (): void {
    $manager = new ThemeManager;
    $manager->use('bootstrap');

    $theme = $manager->resolve();

    expect($theme)->toBeInstanceOf(Bootstrap5Theme::class)
        ->and($theme->name())->toBe('bootstrap5');
});

test('theme manager can switch to bootstrap4', function (): void {
    $manager = new ThemeManager;
    $manager->use('bootstrap4');

    $theme = $manager->resolve();

    expect($theme)->toBeInstanceOf(Bootstrap4Theme::class)
        ->and($theme->name())->toBe('bootstrap4');
});

test('theme manager can register custom theme', function (): void {
    $customTheme = new class implements ThemeContract
    {
        public function name(): string
        {
            return 'custom';
        }

        public function classes(): array
        {
            return ['table' => 'custom-table'];
        }

        public function paginationView(): string
        {
            return 'custom-pagination';
        }

        public function supportsImportantPrefix(): bool
        {
            return false;
        }
    };

    $manager = new ThemeManager;
    $manager->register('custom', $customTheme::class);
    $manager->use('custom');

    $theme = $manager->resolve();

    expect($theme->name())->toBe('custom')
        ->and($theme->classes())->toHaveKey('table');
});

test('tailwind theme returns correct classes', function (): void {
    $theme = new TailwindTheme;
    $classes = $theme->classes();

    expect($classes)->toBeArray()
        ->and($classes)->toHaveKey('table')
        ->and($classes)->toHaveKey('thead')
        ->and($classes)->toHaveKey('tbody')
        ->and($classes)->toHaveKey('th')
        ->and($classes)->toHaveKey('td')
        ->and($classes)->toHaveKey('tr');
});

test('bootstrap5 theme returns correct classes', function (): void {
    $theme = new Bootstrap5Theme;
    $classes = $theme->classes();

    expect($classes)->toBeArray()
        ->and($classes)->toHaveKey('table')
        ->and($classes['table'])->toContain('table');
});

test('bootstrap4 theme returns correct classes', function (): void {
    $theme = new Bootstrap4Theme;
    $classes = $theme->classes();

    expect($classes)->toBeArray()
        ->and($classes)->toHaveKey('table')
        ->and($classes['table'])->toContain('table');
});

test('tailwind theme has filter-label class', function (): void {
    $theme = new TailwindTheme;
    $classes = $theme->classes();

    expect($classes)->toHaveKey('filter-label')
        ->and($classes)->toHaveKey('per-page-label')
        ->and($classes)->toHaveKey('container')
        ->and($classes)->toHaveKey('toolbar')
        ->and($classes)->toHaveKey('toolbar-left')
        ->and($classes)->toHaveKey('toolbar-right')
        ->and($classes)->toHaveKey('toolbar-search')
        ->and($classes)->toHaveKey('toolbar-item')
        ->and($classes)->toHaveKey('toolbar-btn-text')
        ->and($classes)->toHaveKey('filter-btn')
        ->and($classes)->toHaveKey('filter-btn-active')
        ->and($classes)->toHaveKey('filter-badge')
        ->and($classes)->toHaveKey('filter-dropdown')
        ->and($classes)->toHaveKey('filter-range-row')
        ->and($classes)->toHaveKey('filter-range-separator')
        ->and($classes)->toHaveKey('column-btn')
        ->and($classes)->toHaveKey('column-dropdown')
        ->and($classes)->toHaveKey('column-item')
        ->and($classes)->toHaveKey('column-checkbox')
        ->and($classes)->toHaveKey('column-item-label')
        ->and($classes)->toHaveKey('chip-bar')
        ->and($classes)->toHaveKey('chip')
        ->and($classes)->toHaveKey('chip-remove')
        ->and($classes)->toHaveKey('clear-all-btn')
        ->and($classes)->toHaveKey('badge-true')
        ->and($classes)->toHaveKey('badge-false')
        ->and($classes)->toHaveKey('sort-icon')
        ->and($classes)->toHaveKey('sort-icon-active')
        ->and($classes)->toHaveKey('footer')
        ->and($classes)->toHaveKey('results-count')
        ->and($classes)->toHaveKey('pagination-nav')
        ->and($classes)->toHaveKey('filter-clear-wrapper')
        ->and($classes)->toHaveKey('filter-clear-btn');
});

test('tailwind theme has bulk action and selection classes', function (): void {
    $theme = new TailwindTheme;
    $classes = $theme->classes();

    expect($classes)
        ->toHaveKey('bulk-btn')
        ->toHaveKey('bulk-btn-active')
        ->toHaveKey('bulk-badge')
        ->toHaveKey('bulk-dropdown')
        ->toHaveKey('bulk-dropdown-item')
        ->toHaveKey('bulk-checkbox-th')
        ->toHaveKey('bulk-checkbox-td')
        ->toHaveKey('bulk-checkbox')
        ->toHaveKey('selection-bar')
        ->toHaveKey('selection-count')
        ->toHaveKey('selection-actions')
        ->toHaveKey('selection-select-page-btn')
        ->toHaveKey('selection-deselect-page-btn')
        ->toHaveKey('selection-deselect-btn');
});

test('bootstrap5 theme has bulk action and selection classes', function (): void {
    $theme = new Bootstrap5Theme;
    $classes = $theme->classes();

    expect($classes)
        ->toHaveKey('bulk-btn')
        ->toHaveKey('bulk-btn-active')
        ->toHaveKey('bulk-badge')
        ->toHaveKey('bulk-dropdown')
        ->toHaveKey('bulk-dropdown-item')
        ->toHaveKey('bulk-checkbox-th')
        ->toHaveKey('bulk-checkbox-td')
        ->toHaveKey('bulk-checkbox')
        ->toHaveKey('selection-bar')
        ->toHaveKey('selection-count')
        ->toHaveKey('selection-actions')
        ->toHaveKey('selection-select-page-btn')
        ->toHaveKey('selection-deselect-page-btn')
        ->toHaveKey('selection-deselect-btn');
});
test('bootstrap5 theme has filter-label class', function (): void {
    $theme = new Bootstrap5Theme;
    $classes = $theme->classes();

    expect($classes)->toHaveKey('filter-label')
        ->and($classes)->toHaveKey('per-page-label')
        ->and($classes)->toHaveKey('container')
        ->and($classes)->toHaveKey('toolbar')
        ->and($classes)->toHaveKey('toolbar-left')
        ->and($classes)->toHaveKey('toolbar-right')
        ->and($classes)->toHaveKey('toolbar-search')
        ->and($classes)->toHaveKey('toolbar-item')
        ->and($classes)->toHaveKey('toolbar-btn-text')
        ->and($classes)->toHaveKey('filter-btn')
        ->and($classes)->toHaveKey('filter-btn-active')
        ->and($classes)->toHaveKey('filter-badge')
        ->and($classes)->toHaveKey('filter-dropdown')
        ->and($classes)->toHaveKey('filter-range-row')
        ->and($classes)->toHaveKey('filter-range-separator')
        ->and($classes)->toHaveKey('column-btn')
        ->and($classes)->toHaveKey('column-dropdown')
        ->and($classes)->toHaveKey('column-item')
        ->and($classes)->toHaveKey('column-checkbox')
        ->and($classes)->toHaveKey('column-item-label')
        ->and($classes)->toHaveKey('chip-bar')
        ->and($classes)->toHaveKey('chip')
        ->and($classes)->toHaveKey('chip-remove')
        ->and($classes)->toHaveKey('clear-all-btn')
        ->and($classes)->toHaveKey('badge-true')
        ->and($classes)->toHaveKey('badge-false')
        ->and($classes)->toHaveKey('sort-icon')
        ->and($classes)->toHaveKey('sort-icon-active')
        ->and($classes)->toHaveKey('footer')
        ->and($classes)->toHaveKey('results-count')
        ->and($classes)->toHaveKey('pagination-nav')
        ->and($classes)->toHaveKey('filter-clear-wrapper')
        ->and($classes)->toHaveKey('filter-clear-btn');
});

test('bootstrap4 theme has bulk action and selection classes', function (): void {
    $theme = new Bootstrap4Theme;
    $classes = $theme->classes();

    expect($classes)
        ->toHaveKey('bulk-btn')
        ->toHaveKey('bulk-btn-active')
        ->toHaveKey('bulk-badge')
        ->toHaveKey('bulk-dropdown')
        ->toHaveKey('bulk-dropdown-item')
        ->toHaveKey('bulk-checkbox-th')
        ->toHaveKey('bulk-checkbox-td')
        ->toHaveKey('bulk-checkbox')
        ->toHaveKey('selection-bar')
        ->toHaveKey('selection-count')
        ->toHaveKey('selection-actions')
        ->toHaveKey('selection-select-page-btn')
        ->toHaveKey('selection-deselect-page-btn')
        ->toHaveKey('selection-deselect-btn');
});

test('bootstrap4 theme has filter-label class', function (): void {
    $theme = new Bootstrap4Theme;
    $classes = $theme->classes();

    expect($classes)->toHaveKey('filter-label')
        ->and($classes)->toHaveKey('per-page-label')
        ->and($classes)->toHaveKey('container')
        ->and($classes)->toHaveKey('toolbar')
        ->and($classes)->toHaveKey('toolbar-left')
        ->and($classes)->toHaveKey('toolbar-right')
        ->and($classes)->toHaveKey('toolbar-search')
        ->and($classes)->toHaveKey('toolbar-item')
        ->and($classes)->toHaveKey('toolbar-btn-text')
        ->and($classes)->toHaveKey('filter-btn')
        ->and($classes)->toHaveKey('filter-btn-active')
        ->and($classes)->toHaveKey('filter-badge')
        ->and($classes)->toHaveKey('filter-dropdown')
        ->and($classes)->toHaveKey('filter-range-row')
        ->and($classes)->toHaveKey('filter-range-separator')
        ->and($classes)->toHaveKey('column-btn')
        ->and($classes)->toHaveKey('column-dropdown')
        ->and($classes)->toHaveKey('column-item')
        ->and($classes)->toHaveKey('column-checkbox')
        ->and($classes)->toHaveKey('column-item-label')
        ->and($classes)->toHaveKey('chip-bar')
        ->and($classes)->toHaveKey('chip')
        ->and($classes)->toHaveKey('chip-remove')
        ->and($classes)->toHaveKey('clear-all-btn')
        ->and($classes)->toHaveKey('badge-true')
        ->and($classes)->toHaveKey('badge-false')
        ->and($classes)->toHaveKey('sort-icon')
        ->and($classes)->toHaveKey('sort-icon-active')
        ->and($classes)->toHaveKey('footer')
        ->and($classes)->toHaveKey('results-count')
        ->and($classes)->toHaveKey('pagination-nav')
        ->and($classes)->toHaveKey('filter-clear-wrapper')
        ->and($classes)->toHaveKey('filter-clear-btn');
});
