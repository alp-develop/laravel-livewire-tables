# Laravel Livewire Tables

<p align="center">
    <picture>
        <img src="art/banner.png" width="100%" alt="Laravel Livewire Tables">
    </picture>
</p>

<p align="center">
    <a href="https://github.com/alp-develop/laravel-livewire-tables/actions/workflows/tests.yml"><img src="https://github.com/alp-develop/laravel-livewire-tables/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
    <a href="https://packagist.org/packages/alp-develop/laravel-livewire-tables"><img src="https://poser.pugx.org/alp-develop/laravel-livewire-tables/v/stable" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/alp-develop/laravel-livewire-tables"><img src="https://poser.pugx.org/alp-develop/laravel-livewire-tables/license" alt="License"></a>
</p>

> Full-featured, reactive data tables for Laravel. Search, sort, filter, paginate, export, bulk actions — zero JavaScript.

**Laravel** 10–13 | **Livewire** 3–4 | **PHP** 8.1–8.5 | **Tailwind** / **Bootstrap 5** / **Bootstrap 4** | Dark mode

---

## Install

```bash
composer require alp-develop/laravel-livewire-tables
```

If using Tailwind, add to your CSS: `[x-cloak] { display: none !important; }`

## Quick Start

```bash
php artisan make:livewiretable UsersTable User
```

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
                ->label('Joined')->sortable()
                ->format(fn ($value) => $value?
                ->format('M d, Y')),
            ActionColumn::make()
                ->button('Edit', fn ($row) => "edit({$row->id})", 'lt-btn-primary')
                ->button('Delete', fn ($row) => "delete({$row->id})", 'lt-btn-primary'),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('active')
                ->label('Status')
                ->setOptions(['' => 'All', '1' => 'Active', '0' => 'Inactive'])
                ->filter(fn (Builder $q, $v) => $q->where('active', (bool) $v)),
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

```blade
<livewire:tables.users-table />
```

## Features

- **Search** — Full-text across columns with debounce and join alias support
- **Sorting** — Single or multi-column with configurable direction
- **Filters** — Text, Select, Boolean, Number, NumberRange, Date, DateRange, MultiDate (with dependent/cascading)
- **Bulk Actions** — Exclusion-based select-all across pages + CSV export
- **Column Types** — Text, Boolean, Date, Image (lightbox), Blade (custom views), Action (buttons)
- **Themes** — Tailwind, Bootstrap 5, Bootstrap 4 with dark mode and custom color palette
- **State Persistence** — Search, filters, sort cached in session
- **Toolbar Slots** — 6 injection points for custom content
- **Lifecycle Hooks** — `onQuerying`, `onQueried`, `onRendering`, `onRendered`
- **14 Languages** — EN, ES, PT, FR, DE, IT, NL, PL, RU, ZH, JA, KO, TR, ID

## Color Customization

In `config/livewire-tables.php`:

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

## Documentation

| Guide | |
|-------|---|
| [Installation](docs/installation.md) | Setup, config, publishing assets |
| [Columns](docs/columns.md) | Text, Boolean, Date, Image, Action, Blade |
| [Filters](docs/filters.md) | All types, dependent filters, custom logic |
| [Bulk Actions](docs/bulk-actions.md) | Selection model, custom actions, CSV export |
| [Export](docs/export.md) | Auto CSV, custom exports |
| [Events & Hooks](docs/events.md) | Lifecycle hooks, external refresh |
| [Toolbar Slots](docs/toolbar-slots.md) | 6 hook points for custom content |
| [Theming](docs/theming.md) | Themes, dark mode, color palette |
| [Configuration](docs/configuration.md) | Config reference, per-table options |
| [Joins](docs/joins.md) | Joined columns, aliases, search on joins |
| [Security](docs/security.md) | Built-in protections, safe callbacks |

## Development

```bash
composer test       # Run tests
composer analyse    # PHPStan level 8
composer format     # Pint code style
```

[Contributing](CONTRIBUTING.md) · [Changelog](CHANGELOG.md) · [License](LICENSE)
