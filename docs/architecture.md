# Architecture

**Contents:**
- [Overview](#overview)
- [Engine and Pipeline Steps](#engine-and-pipeline-steps)
- [Adding a Custom Step](#adding-a-custom-step)
- [State Object](#state-object)
- [Contracts](#contracts)
- [Trait Composition](#trait-composition)

## Overview

The package uses a pipeline pattern to build and execute table queries:

```
DataTableComponent
  └─ Engine
       ├─ SearchStep   → applies LIKE search across searchable columns
       ├─ FilterStep   → applies each active filter value
       └─ SortStep     → applies ORDER BY
```

The `Engine` is created once per Livewire request and cached on the component. `render()`, `getSelectedIds()`, and `exportCsvAuto()` all reuse the same `Engine` instance.

## Engine and Pipeline Steps

`Engine::process()` applies all registered steps and then paginates. `Engine::applySteps()` applies steps without paginating — used by export and bulk selection to avoid a redundant `COUNT(*)`.

```php
// Internal usage
$engine = (new Engine($columns, $filters))
    ->addStep(new SearchStep($columns))
    ->addStep(new FilterStep($filters))
    ->addStep(new SortStep($columns));

// With pagination
$paginator = $engine->process($query, $state);

// Without pagination (export, bulk)
$builder = $engine->applySteps($query, $state);
```

## Adding a Custom Step

Implement `StepContract` and register it in `configure()`:

```php
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;

class SoftDeleteStep implements StepContract
{
    public function apply(Builder $query, StateContract $state): Builder
    {
        return $query->withTrashed();
    }
}
```

Register it by adding it in `configure()` — or by overriding `getEngine()` for full pipeline control:

```php
// Option 1 (recommended): Override configure() — no pipeline re-definition needed
// This is for per-component query scoping; use custom steps for generic behavior.

// Option 2: Override getEngine() for complete pipeline customisation
class MyTable extends DataTableComponent
{
    protected function getEngine(): Engine
    {
        if ($this->cachedEngine !== null) {
            return $this->cachedEngine;
        }

        $columns = $this->resolveColumns();
        $filters = $this->resolveFilters();

        $this->cachedEngine = (new Engine($columns, $filters))
            ->addStep(new SearchStep($columns))
            ->addStep(new FilterStep($filters))
            ->addStep(new SortStep($columns))
            ->addStep(new SoftDeleteStep);

        return $this->cachedEngine;
    }
}
```

> **Note:** `Engine` is a concrete class (not `final`) and `getEngine()` is `protected`. Override either to customise the pipeline. Always assign to `$this->cachedEngine` before returning so that `render()`, `getSelectedIds()`, and export reuse the same instance.

## State Object

`State` is a value object passed to each step:

```php
new State(
    search: string,
    sortFields: array,   // ['field' => 'asc'|'desc', ...]
    filters: array,      // ['filter_key' => $value, ...]
    perPage: int,
    page: int,
)
```

Steps receive `StateContract` — they can read search, filters, and sort state but cannot modify it.

## Contracts

| Contract | Description |
|----------|-------------|
| `EngineContract` | `process()`, `applySteps()`, `addStep()`, `columns()`, `filters()` |
| `StepContract` | `apply(Builder, StateContract): Builder` |
| `FilterContract` | `run(Builder, mixed): Builder`, `normalizeValue(mixed): mixed` |
| `ColumnContract` | `field()`, `getLabel()`, `isVisible()`, `isSortable()`, `isSearchable()` |
| `StateContract` | `search()`, `sortFields()`, `filters()`, `perPage()`, `page()` |

## Trait Composition

`DataTableComponent` uses 11 traits. Each trait has `@requires` docblocks documenting its dependencies:

| Trait | Responsibilities |
|-------|----------------|
| `HasColumns` | `columns()` resolution, column caching, toggle hidden |
| `HasFilters` | `filters()` resolution, filter state management |
| `HasSearch` | Search term with length clamping |
| `HasSorting` | Sort field/direction state |
| `HasPerPage` | Per-page with whitelist validation |
| `HasBulkActions` | Selected IDs, excluded IDs, bulk action dispatch |
| `HasExport` | CSV streaming export with formula injection prevention |
| `HasStateCache` | Session persistence and dirty tracking |
| `HasListeners` | Event-based refresh |
| `HasPagination` | Pagination view resolution |
| `HasConfiguration` | `configure()` hook for per-request setup |
