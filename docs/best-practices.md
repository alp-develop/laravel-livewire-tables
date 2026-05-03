# Best Practices

**Contents:**
- [Authorization](#authorization)
- [Row-Level Security](#row-level-security)
- [Bulk Actions](#bulk-actions)
- [Export](#export)
- [Performance](#performance)
- [Custom Callbacks](#custom-callbacks)

## Authorization

The package does not enforce authorization. All access control is the responsibility of the application. Apply authorization at two levels:

**1. Component mount** — prevent unauthorized users from loading the table at all:

```php
class OrdersTable extends DataTableComponent
{
    public function mount(): void
    {
        $this->authorize('viewAny', Order::class);
        $this->build();
    }
}
```

**2. Base query** — scope the query so users only see their own data:

```php
public function query(): Builder
{
    return Order::query()->where('user_id', auth()->id());
}
```

Scoping the base query is the safest approach. It prevents data leaks even if other controls fail.

## Row-Level Security

Never trust IDs from the client. Always re-validate in the base query:

```php
// DANGEROUS — no row-level scoping
public function query(): Builder
{
    return Invoice::query();
}

// SAFE — user can only access their own invoices
public function query(): Builder
{
    return Invoice::query()->where('company_id', auth()->user()->company_id);
}
```

This also protects `getSelectedIds()` — it always intersects against the current query result, so IDs outside the user's scope are silently excluded.

## Bulk Actions

Bulk actions receive the result of `getSelectedIds()`, which has already been intersected with the current filtered query. However, the underlying data may change between selection and execution (TOCTOU). Apply authorization inside every bulk action:

```php
public function deleteSelected(): void
{
    $ids = $this->getSelectedIds();

    // Re-validate authorization before acting
    Order::whereIn('id', $ids)
        ->where('user_id', auth()->id())   // enforce row-level scope
        ->each(fn ($order) => $this->authorize('delete', $order));

    Order::whereIn('id', $ids)
        ->where('user_id', auth()->id())
        ->delete();
}
```

## Export

`exportCsvAuto()` exports the current filtered result set. Ensure the base query scopes data before exporting:

```php
class ReportsTable extends DataTableComponent
{
    public function query(): Builder
    {
        // Only export data this user can access
        return Report::query()->where('team_id', auth()->user()->team_id);
    }
}
```

For sensitive exports, add an explicit authorization check:

```php
public function exportCsvAuto(): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $this->authorize('export', Report::class);

    return parent::exportCsvAuto();
}
```

Override `selectColumns()` to limit exported fields and avoid exposing sensitive columns:

```php
protected function selectColumns(): array
{
    return ['id', 'title', 'status', 'created_at'];
    // Omits: internal_notes, payment_reference, user_token, etc.
}
```

## Performance

- **Index every sorted and searched column.** Without indexes, LIKE scans and ORDER BY queries degrade linearly with table size.
- **Use `selectColumns()`.** Returning only the columns your table needs avoids fetching large text blobs or sensitive fields.
- **Cache filter options.** If `SelectFilter::setOptions()` data comes from a database, wrap it in `Cache::remember()`:

```php
SelectFilter::make('category')
    ->setOptions(Cache::remember('cats', 60, fn () =>
        Category::pluck('name', 'id')->toArray()
    )),
```

- **Use `joins()` for related data.** Accessing Eloquent relationships inside column formatters triggers N+1 queries. Use `->join()` in the base query instead:

```php
public function query(): Builder
{
    return Order::query()
        ->join('users', 'users.id', '=', 'orders.user_id')
        ->select('orders.*', 'users.name as user_name');
}
```

- **Set `search_debounce`.** In `config/livewire-tables.php`, increase to 400–500ms for heavy queries to reduce server load.

## Custom Callbacks

All custom `filter()`, `searchable()`, and action closures must use Eloquent parameter binding. Never concatenate user input into raw SQL:

```php
// SAFE
TextFilter::make('description')
    ->filter(fn ($q, $v) => $q->where('description', 'LIKE', "%{$v}%")),

// DANGEROUS — SQL injection
TextFilter::make('description')
    ->filter(fn ($q, $v) => $q->whereRaw("description LIKE '%{$v}%'")),
```

For action buttons that pass data to JavaScript, use integer IDs or escape values:

```php
// SAFE — integer ID cannot contain injection payload
ActionColumn::make()
    ->button('Edit', fn ($row) => "editOrder({$row->id})"),

// RISKY — string slug must be trusted/validated server-side
ActionColumn::make()
    ->button('View', fn ($row) => "viewOrder('{$row->slug}')"),
```
