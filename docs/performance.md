# Performance

**Contents:**
- [Built-in Optimizations](#built-in-optimizations)
- [selectColumns Hook](#selectcolumns-hook)
- [Engine Pipeline](#engine-pipeline)
- [Tips](#tips)

## Built-in Optimizations

The package applies several performance optimizations automatically:

| Optimization | Description |
|-------------|-------------|
| **configure() deduplication** | `configure()` runs once per Livewire request cycle, not on every `render()` re-evaluation. |
| **Engine caching** | The `Engine` instance (with its 3 pipeline steps) is built once per request and reused across `render()`, `getSelectedIds()`, and export. |
| **getAllColumns() caching** | The filtered column list (excluding hidden/conditional columns) is computed once and cached on the trait property. |
| **getVisibleColumns() caching** | The visible column list (filtered + not hidden by user toggle) is also computed once and cached per request. |
| **ThemeManager caching** | The `ThemeManager` singleton is resolved once and cached on the component. |
| **Session dirty tracking** | `dehydrate()` only writes table state to the session when the state has actually changed, avoiding unnecessary session writes on every request. |
| **Export pipeline deduplication** | The export query reuses the already-built query pipeline; bulk selected IDs are computed from a clone of the same query instead of running a second pipeline. |
| **buildActiveFilterChips() O(n)** | Active filter chip labels are resolved in a single pass using a pre-built parent value map, avoiding nested loops. |
| **SelectFilter options caching** | `resolveOptions()` for dependent (child) filters caches callback results per parent value **within a single request only** (stored in `$resolvedOptionsCache` on the filter instance). This eliminates redundant DB calls during a single render cycle. For cross-request caching, implement your own wrapper using `Cache::remember()`. |

## selectColumns Hook

By default, table queries use `SELECT *`. Override `selectColumns()` in your table component to return only the columns your table actually needs:

```php
protected function selectColumns(): array
{
    return ['id', 'name', 'email', 'created_at'];
}
```

This avoids fetching large text columns or columns only needed by other features. The method is called automatically in export builds.

## Engine Pipeline

The query pipeline runs three steps in order:

1. **SearchStep** — applies `LIKE` constraints for the active search term across all searchable columns.
2. **FilterStep** — applies each active filter value via `Filter::run()`.
3. **SortStep** — applies `ORDER BY` for the active sort field and direction.

`Engine::applySteps()` runs the pipeline without paginating, which lets export and bulk-select builds reuse the same filtered/sorted query without an extra `COUNT(*)` from pagination.

## Tips

- Use `joins()` for eager loading instead of Eloquent relationships inside column formatters to avoid N+1 queries.
- Use `searchable(fn ...)` custom callbacks with indexed columns to avoid full-table LIKE scans.
- Set `search_debounce` in the config to 400–500ms for tables with heavy search queries.
- Index any column you sort or filter on regularly.

## SelectFilter Options

`SelectFilter::setOptions()` accepts a static array. If options come from a database query, cache the result to avoid a query on every render:

```php
// Avoid — runs a query on every Livewire request
SelectFilter::make('category')
    ->setOptions(Category::pluck('name', 'id')->toArray()),

// Prefer — cached for 60 seconds
SelectFilter::make('category')
    ->setOptions(Cache::remember('category-options', 60, fn () =>
        Category::pluck('name', 'id')->toArray()
    )),
```

Dependent filters (`->parentFilter(fn ...)`) call the callback on every render while the parent filter has a value. Apply the same caching pattern inside the callback.
