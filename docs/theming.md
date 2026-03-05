# Theming

The theme system uses a driver pattern. Themes implement `ThemeContract` and are registered via `ThemeManager`.

## Built-in Themes

| Theme | Config Value | Description |
|-------|-------------|-------------|
| Tailwind CSS | `tailwind` | Default theme with utility classes |
| Bootstrap 5 | `bootstrap` | Bootstrap 5 component classes |

## Configuration

Set the active theme in `config/livewire-tables.php`:

```php
'theme' => 'tailwind', // or 'bootstrap'
```

## Primary Color Customization

Both themes use CSS custom properties (`--lt-primary-*`) for the primary color. Everything is configurable from the config file:

```php
'colors' => [
    '50'  => '#f0fdfa',
    '100' => '#ccfbf1',
    '200' => '#99f6e4',
    '400' => '#2dd4bf',
    '500' => '#14b8a6',
    '600' => '#0d9488',
    '700' => '#0f766e',
],
```

See [Configuration - Color Palette](configuration.md#color-palette) for presets and CSS override options.

## How Themes Work

Each theme provides a `classes()` array that maps UI elements to CSS classes:

```php
use Livewire\Tables\Core\Contracts\ThemeContract;

final class TailwindTheme implements ThemeContract
{
    public function name(): string
    {
        return 'tailwind';
    }

    public function classes(): array
    {
        return [
            'container' => 'bg-white rounded-xl shadow-sm border border-gray-200',
            'table'     => 'min-w-full divide-y divide-gray-200',
            'thead'     => 'bg-gray-50/80',
            'th'        => 'px-4 py-3 text-left text-sm font-bold text-gray-600',
            'tbody'     => 'bg-white divide-y divide-gray-100',
            'tr'        => 'hover:bg-gray-50/60 transition-colors',
            'td'        => 'px-4 py-3 text-sm text-gray-700',
        ];
    }

    public function paginationView(): string
    {
        return 'livewire-tables::components.pagination.tailwind';
    }

    public function supportsImportantPrefix(): bool
    {
        return true;
    }
}
```

### ThemeContract Interface

| Method | Description |
|--------|-------------|
| `name()` | Theme identifier string |
| `classes()` | Array mapping CSS class keys to class strings |
| `paginationView()` | Blade view used for pagination |
| `supportsImportantPrefix()` | Whether `!` prefix works (Tailwind = true, Bootstrap = false) |

## Available CSS Keys

The theme provides classes for all UI elements:

| Key | Element |
|-----|---------|
| `container` | Main table wrapper |
| `toolbar` | Top toolbar area |
| `toolbar-row` | Toolbar flex row |
| `toolbar-left` | Left side of toolbar |
| `toolbar-right` | Right side of toolbar |
| `search-input` | Search text input |
| `filter-btn` / `filter-btn-active` | Filter toggle button |
| `filter-dropdown` | Filter panel |
| `filter-group` / `filter-select` / `filter-input` / `filter-label` | Filter elements |
| `table` / `thead` / `th` / `tbody` / `tr` / `td` | Table structure |
| `th-sortable` / `th-sorted` | Sortable headers |
| `chip-bar` / `chip` / `chip-remove` | Active filter/sort chips |
| `pagination-wrapper` / `per-page-select` | Pagination |
| `column-btn` / `column-dropdown` | Column visibility |
| `bulk-btn` / `bulk-btn-active` / `bulk-badge` | Bulk actions |
| `bulk-checkbox-th` / `bulk-checkbox-td` / `bulk-checkbox` | Row selection |
| `selection-bar` / `selection-count` | Selection info bar |
| `badge-true` / `badge-false` | Boolean column badges |
| `empty-state` | No results message |

## Creating a Custom Theme

1. Create a class implementing `ThemeContract`:

```php
namespace App\Themes;

use Livewire\Tables\Core\Contracts\ThemeContract;

final class MaterialTheme implements ThemeContract
{
    public function name(): string
    {
        return 'material';
    }

    public function classes(): array
    {
        return [
            'container' => 'mdc-data-table',
            'table'     => 'mdc-data-table__table',
            'thead'     => 'mdc-data-table__header-row',
            'th'        => 'mdc-data-table__header-cell',
            'tbody'     => 'mdc-data-table__content',
            'tr'        => 'mdc-data-table__row',
            'td'        => 'mdc-data-table__cell',
            // ... all other keys
        ];
    }

    public function paginationView(): string
    {
        return 'components.pagination.material';
    }

    public function supportsImportantPrefix(): bool
    {
        return false;
    }
}
```

2. Register it in a service provider:

```php
use Livewire\Tables\Themes\ThemeManager;

public function boot(): void
{
    $manager = app(ThemeManager::class);
    $manager->register('material', \App\Themes\MaterialTheme::class);
    $manager->use('material');
}
```

## Per-Component Styling

Override CSS classes per table in `configure()`:

```php
public function configure(): void
{
    $this->setHeadClass('bg-blue-100 text-blue-800');
    $this->setBodyClass('text-gray-600');
    $this->setRowClass(fn($row) => $row->active ? 'bg-green-50' : 'bg-red-50');
}
```

### Toolbar Buttons

```php
public function configure(): void
{
    $this->setFilterBtnClass('btn btn-outline-info btn-sm');
    $this->setFilterBtnActiveClass('btn btn-info btn-sm');
    $this->setColumnBtnClass('btn btn-outline-info btn-sm');
    $this->setBulkBtnClass('btn btn-outline-info btn-sm disabled');
    $this->setBulkBtnActiveClass('btn btn-info btn-sm');
}
```

### Filter Styling

```php
public function configure(): void
{
    $this->setFilterGroupClass('mb-4');
    $this->setFilterLabelClass('font-bold text-sm');
    $this->setFilterInputClass('border-2 border-blue-300');
}
```

## Publishing Views

```bash
php artisan vendor:publish --tag=livewire-tables-views
```

Views are published to `resources/views/vendor/livewire-tables/`.
