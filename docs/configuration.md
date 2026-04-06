# Configuration

**Contents:**
- [Global Configuration](#global-configuration)
- [Table Key](#table-key)
- [Translations](#translations)
- [Color Palette](#color-palette)
- [Per-Table Configuration](#per-table-configuration)
- [Available Methods](#available-methods)
- [Lifecycle Hooks](#lifecycle-hooks)

## Global Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=livewire-tables-config
```

This creates `config/livewire-tables.php`:

```php
return [
    'theme' => 'tailwind',
    'colors' => [
        '50'  => '#f0fdfa',
        '100' => '#ccfbf1',
        '200' => '#99f6e4',
        '400' => '#2dd4bf',
        '500' => '#14b8a6',
        '600' => '#0d9488',
        '700' => '#0f766e',
    ],
    'dark_mode' => [
        'enabled' => false,
        'selector' => 'lt-dark',
        'colors' => [
            'bg' => '#0f172a',
            'bg-card' => '#1e293b',
            'bg-subtle' => '#334155',
            'border' => '#334155',
            'text' => '#f1f5f9',
            'text-muted' => '#94a3b8',
        ],
    ],
    'search_debounce' => 300,
    'component_namespace' => 'Tables',
];
```

| Option | Default | Description |
|--------|---------|-------------|
| `theme` | `tailwind` | CSS framework theme used to render all table components. Supports `'tailwind'`, `'bootstrap-5'`, `'bootstrap-4'` and their aliases (see table below). |
| `colors` | Teal palette | Primary color palette with 7 shades (50, 100, 200, 400, 500, 600, 700). Used across buttons, checkboxes, pagination, badges, and selection bar. Maps to CSS variables `--lt-primary-50` through `--lt-primary-700`. Works identically for Tailwind and Bootstrap. |
| `dark_mode.enabled` | `false` | Enables dark mode support. Injects dark CSS rules scoped to the `lt-dark` class on `<html>`. |
| `dark_mode.selector` | `lt-dark` | Session key used to detect dark mode state server-side. Activate with: `session(['lt-dark' => true])`. |
| `dark_mode.colors` | Slate palette | Dark mode color palette with 6 keys: `bg` (page background), `bg-card` (panel/table background), `bg-subtle` (thead, stripes, hover), `border` (borders/dividers), `text` (primary text), `text-muted` (secondary text). |
| `search_debounce` | `300` | Milliseconds to wait after the user stops typing before executing the search query. Recommended: 300–500 for a smooth experience. |
| `component_namespace` | `Tables` | Subdirectory inside `app/Livewire/` where `make:livewiretable` generates new table components. Example: `'Tables'` generates in `app/Livewire/Tables/`. |

### Available Themes

| Theme | Value | Aliases |
|-------|-------|---------|
| Tailwind CSS | `tailwind` | — |
| Bootstrap 5 | `bootstrap-5` | `bootstrap5`, `bootstrap` |
| Bootstrap 4 | `bootstrap-4` | `bootstrap4` |

```php
'theme' => 'tailwind',
'theme' => 'bootstrap5',
'theme' => 'bootstrap4',
```

## Table Key

Each table uses a `tableKey` to isolate its state (filters, search, sorting, pagination) in the session. By default, the key is derived from the class name.

When rendering **multiple instances** of the same table component in one view, assign a unique `table-key` to each one:

```blade
<livewire:tables.users-table table-key="users-active" />
<livewire:tables.users-table table-key="users-archived" />
```

You can also pass it dynamically:

```blade
<livewire:tables.users-table :table-key="'users-' . $section" :table-theme="$theme" />
```

Or set it directly in the component class:

```php
class UsersTable extends DataTableComponent
{
    public string $tableKey = 'users';
}
```

> If you don't set `table-key`, all instances of the same component will share state via session.

## Translations

The package includes translations for 14 languages:

| Language | Code |
|----------|------|
| English | `en` |
| Spanish | `es` |
| Portuguese | `pt` |
| French | `fr` |
| German | `de` |
| Italian | `it` |
| Dutch | `nl` |
| Polish | `pl` |
| Russian | `ru` |
| Chinese | `zh` |
| Japanese | `ja` |
| Korean | `ko` |
| Turkish | `tr` |
| Indonesian | `id` |

Publish translations to customize them:

```bash
php artisan vendor:publish --tag=livewire-tables-translations
```

Files will be copied to `lang/vendor/livewire-tables/`.

For dark mode setup and activation methods, see [Dark Mode](dark-mode.md).

## Color Palette

The `colors` key defines the primary color used across all table UI elements: buttons, checkboxes, pagination, filter badges, selection bar, sort icons, etc.

These map to CSS custom properties `--lt-primary-50` through `--lt-primary-700` and work identically for both Tailwind and Bootstrap themes.

### Presets

| Preset | 50 | 100 | 200 | 400 | 500 | 600 | 700 |
|--------|----|-----|-----|-----|-----|-----|-----|
| Teal (default) | `#f0fdfa` | `#ccfbf1` | `#99f6e4` | `#2dd4bf` | `#14b8a6` | `#0d9488` | `#0f766e` |
| Indigo | `#eef2ff` | `#e0e7ff` | `#c7d2fe` | `#818cf8` | `#6366f1` | `#4f46e5` | `#4338ca` |
| Sky | `#f0f9ff` | `#e0f2fe` | `#bae6fd` | `#38bdf8` | `#0ea5e9` | `#0284c7` | `#0369a1` |
| Purple | `#faf5ff` | `#f3e8ff` | `#e9d5ff` | `#c084fc` | `#a855f7` | `#9333ea` | `#7e22ce` |
| Rose | `#fff1f2` | `#ffe4e6` | `#fecdd3` | `#fb7185` | `#f43f5e` | `#e11d48` | `#be123c` |
| Amber | `#fffbeb` | `#fef3c7` | `#fde68a` | `#fbbf24` | `#f59e0b` | `#d97706` | `#b45309` |

### Example: Switch to Indigo

```php
'colors' => [
    '50'  => '#eef2ff',
    '100' => '#e0e7ff',
    '200' => '#c7d2fe',
    '400' => '#818cf8',
    '500' => '#6366f1',
    '600' => '#4f46e5',
    '700' => '#4338ca',
],
```

### CSS Override

You can also override the colors via CSS without changing the config:

```css
:root {
    --lt-primary-50: #eef2ff;
    --lt-primary-100: #e0e7ff;
    --lt-primary-200: #c7d2fe;
    --lt-primary-400: #818cf8;
    --lt-primary-500: #6366f1;
    --lt-primary-600: #4f46e5;
    --lt-primary-700: #4338ca;
}
```

### Scoped Override

Apply a different color to a specific table:

```css
.custom-table {
    --lt-primary-600: #db2777;
    --lt-primary-700: #be185d;
}
```

```blade
<div class="custom-table">
    <livewire:users-table />
</div>
```

## Per-Table Configuration

Override the `configure()` method in your table component:

```php
public function configure(): void
{
    $this->setDefaultPerPage(25);
    $this->setPerPageOptions([10, 25, 50, 100, 250]);
    $this->setSearchDebounce(500);
    $this->setDefaultSortDirection('desc');
    $this->setEmptyMessage('No records found.');
}
```

## Available Methods

### Pagination

| Method | Description |
|--------|-------------|
| `setDefaultPerPage(int)` | Initial rows per page |
| `setPerPageOptions(array)` | Available per-page choices |

### Search

| Method | Description |
|--------|-------------|
| `setSearchDebounce(int)` | Debounce delay in milliseconds (0-5000) |

### Sorting

| Method | Description |
|--------|-------------|
| `setDefaultSortDirection(string)` | Default sort direction (`asc` or `desc`) |

When a column is sorted for the first time, it uses this direction. Subsequent clicks cycle: initial → opposite → clear.

### Empty State

| Method | Description |
|--------|-------------|
| `setEmptyMessage(string)` | Message when no results found |

Falls back to the translation key `livewire-tables::messages.no_results`.

### Table Styling

| Method | Description |
|--------|-------------|
| `setHeadClass(string)` | CSS class for `<thead>` |
| `setBodyClass(string)` | CSS class for `<tbody>` |
| `setRowClass(string\|Closure)` | CSS class for `<tr>` (static or per-row) |

```php
$this->setRowClass(fn($row) => $row->priority === 'high' ? 'bg-red-50' : '');
```

### Filter Styling

| Method | Description |
|--------|-------------|
| `setFilterGroupClass(string)` | CSS class for filter wrapper |
| `setFilterLabelClass(string)` | CSS class for filter labels |
| `setFilterInputClass(string)` | CSS class for filter inputs |

### Toolbar Button Styling

| Method | Description |
|--------|-------------|
| `setFilterBtnClass(string)` | Filter button (normal state) |
| `setFilterBtnActiveClass(string)` | Filter button when filters are active |
| `setColumnBtnClass(string)` | Column visibility button |
| `setBulkBtnClass(string)` | Bulk actions button (disabled state) |
| `setBulkBtnActiveClass(string)` | Bulk actions button when items are selected |

When not set, buttons use the theme's default classes.

## Lifecycle Hooks

| Method | When | Purpose |
|--------|------|---------|
| `configure()` | After boot | Set table options |
| `build()` | After mount | Custom initialization logic |
| `query()` | On render | Return the base Eloquent Builder |
| `columns()` | On render (cached) | Define columns |
| `filters()` | On render | Define filters |
| `bulkActions()` | On render | Define bulk actions |

## Execution Order

```
mount() → configure() → build() → query() → columns() → filters() → Engine::process() → render()
```

On subsequent Livewire requests:

```
boot() → configure() → query() → Engine::process() → render()
```


