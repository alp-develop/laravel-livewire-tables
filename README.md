# Laravel Livewire Tables

> **The easiest way to build full-featured, reactive data tables in Laravel.**  
> Search, sort, filter, paginate, export to CSV, bulk actions — zero custom JavaScript required.

[![Tests](https://github.com/alp-develop/laravel-livewire-tables/actions/workflows/tests.yml/badge.svg)](https://github.com/alp-develop/laravel-livewire-tables/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/alp-develop/laravel-livewire-tables/v/stable)](https://packagist.org/packages/alp-develop/laravel-livewire-tables)
[![License](https://poser.pugx.org/alp-develop/laravel-livewire-tables/license)](https://packagist.org/packages/alp-develop/laravel-livewire-tables)

A powerful, server-side data table component for **Laravel 10, 11, 12** and **Livewire 3, 4**. Designed for real-world applications that need sorting, searching, filtering, bulk actions, CSV export, dark mode, and multiple theme support — all in a single, easy-to-use package. Works with **PHP 8.1 through 8.4**, **Tailwind CSS**, **Bootstrap 5**, and **Bootstrap 4**.

---

## Why This Package?

| | |
|---|---|
| **Zero custom JS** | 100% server-side. Powered by Livewire + [Alpine.js](https://alpinejs.dev/) for reactive UI interactions. |
| **Multi-version** | Laravel 10 / 11 / 12, Livewire 3 / 4, PHP 8.1–8.4 — fully tested. |
| **3 themes** | Tailwind CSS, Bootstrap 5, Bootstrap 4 — all with dark mode. |
| **Production-ready** | 184 tests, 479 assertions across 9 PHP/Laravel/Livewire matrix combinations. |
| **One command** | `php artisan make:livewiretable UsersTable User` — auto-detects columns. |

## Features

- **Search** — Global full-text search across multiple columns with debounce and join alias support
- **Sorting** — Single or multi-column sorting with configurable default direction
- **Filters** — Text, Select, Boolean, Number, NumberRange, Date, DateRange, MultiDate with dependent/cascading support
- **Pagination** — Laravel native pagination with configurable per-page dropdown
- **Bulk Actions** — Exclusion-based selection model (instant select-all across pages)
- **CSV Export** — One-click CSV export from visible columns, or build custom exports
- **Column Types** — Text, Boolean, Date, Image (with lightbox), Blade (custom views), Action (buttons)
- **Toolbar Slots** — 6 injection points to add custom content around the table
- **Lifecycle Hooks** — `onQuerying`, `onQueried`, `onRendering`, `onRendered` + Livewire event dispatch
- **Dark Mode** — Full dark mode support for all 3 themes, reactive toggle
- **Themes** — Tailwind CSS, Bootstrap 5, Bootstrap 4 with configurable color palette
- **Translations** — English, Spanish, Portuguese included (easily extendable)
- **State Persistence** — Search, filters, sort, and pagination state cached in session
- **External Refresh** — Dispatch events to refresh tables from other Livewire components
- **Filter Events** — `table-filters-applied` event dispatched when filters or search change
- **Artisan Generator** — `php artisan make:livewiretable UsersTable User` with column auto-detection

## Compatibility Matrix

| PHP | Laravel | Livewire | Testbench |
|-----|---------|----------|-----------|
| 8.1 | 10.x   | 3.x     | 8.x       |
| 8.2 | 10.x   | 3.x     | 8.x       |
| 8.2 | 11.x   | 3.x     | 9.x       |
| 8.3 | 11.x   | 3.x     | 9.x       |
| 8.2 | 11.x   | 4.x     | 9.x       |
| 8.3 | 11.x   | 4.x     | 9.x       |
| 8.2 | 12.x   | 4.x     | 10.x      |
| 8.3 | 12.x   | 4.x     | 10.x      |
| 8.4 | 12.x   | 4.x     | 10.x      |

## Requirements

- **PHP** 8.1, 8.2, 8.3, or 8.4
- **Laravel** 10.x, 11.x, or 12.x
- **Livewire** 3.x or 4.x
- **Alpine.js** (included automatically with Livewire)

## Quick Start

### 1. Install

```bash
composer require alp-develop/laravel-livewire-tables
```

### 2. Tailwind CSS Setup

If you're using the **Tailwind** theme, add this to your app CSS to prevent Alpine.js flicker:

```css
[x-cloak] { display: none !important; }
```

> **Note:** Bootstrap themes do not require this — Alpine.js handles visibility automatically through Livewire.

### 3. Create a Table

```bash
php artisan make:livewiretable UsersTable User
```

### 4. Define Your Table

```php
<?php

namespace App\Livewire\Tables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Columns\BooleanColumn;
use Livewire\Tables\Columns\ActionColumn;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class UsersTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setDefaultPerPage(25);
        $this->setSearchDebounce(300);
        $this->setDefaultSortDirection('desc');
        $this->setEmptyMessage('No users found.');
    }

    public function query(): Builder
    {
        return User::query();
    }

    public function columns(): array
    {
        return [
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('email')->sortable()->searchable(),
            BooleanColumn::make('active')->sortable(),
            TextColumn::make('created_at')
                ->label('Joined')
                ->sortable()
                ->format(fn($value) => $value?->format('M d, Y')),
            ActionColumn::make()
                ->button('Edit', fn($row) => "edit({$row->id})", 'lt-btn-primary')
                ->button('Delete', fn($row) => "delete({$row->id})", 'lt-btn-primary'),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('active')
                ->label('Status')
                ->setOptions(['' => 'All', '1' => 'Active', '0' => 'Inactive'])
                ->filter(fn(Builder $q, $v) => $q->where('active', (bool) $v)),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'deleteSelected' => 'Delete Selected',
            'exportCsvAuto'  => 'Export CSV',
        ];
    }

    public function deleteSelected(): void
    {
        User::whereIn('id', $this->getSelectedIds())->delete();
    }

    public function edit(int $id): void
    {
        $this->redirect(route('users.edit', $id));
    }

    public function delete(int $id): void
    {
        User::findOrFail($id)->delete();
    }
}
```

### 5. Use in Blade

```blade
<livewire:tables.users-table />
```

## Color Customization

Change the primary color palette in `config/livewire-tables.php`:

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

Works identically for both Tailwind and Bootstrap themes.

## Documentation

| Guide | Description |
|-------|-------------|
| [Installation](docs/installation.md) | Installation, configuration, publishing assets |
| [Columns](docs/columns.md) | Text, Boolean, Date, Image, Action, Blade columns |
| [Filters](docs/filters.md) | All filter types, dependent filters, custom logic |
| [Bulk Actions](docs/bulk-actions.md) | Selection model, custom actions, CSV export |
| [Export](docs/export.md) | Auto CSV export, custom exports, configuration |
| [Events & Hooks](docs/events.md) | Lifecycle hooks, Livewire dispatch, external refresh |
| [Toolbar Slots](docs/toolbar-slots.md) | 6 hook points for custom toolbar content |
| [Theming](docs/theming.md) | Tailwind, Bootstrap, custom themes, color palette |
| [Configuration](docs/configuration.md) | Config file reference, per-table options, colors |
| [Joins](docs/joins.md) | Joined table columns, aliases, search on joins |

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## Code Style

```bash
composer format
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for version history.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
