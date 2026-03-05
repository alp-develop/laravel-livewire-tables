# Events & Listeners

## External Refresh Events

Every table automatically listens for two events:

| Event | Scope | Description |
|-------|-------|-------------|
| `livewire-tables-refresh` | Global | Refreshes **all** tables on the page |
| `{tableKey}-refresh` | Targeted | Refreshes a **specific** table |

### Global Refresh

```php
$this->dispatch('livewire-tables-refresh');
```

### Targeted Refresh

```php
class UserTable extends DataTableComponent
{
    public string $tableKey = 'users';
}

// From another component:
$this->dispatch('users-refresh');
```

### From Alpine.js

```html
<button @click="$dispatch('users-refresh')">Refresh Users</button>
```

### Custom Event Name

```php
class UserTable extends DataTableComponent
{
    protected string $refreshEvent = 'reload-user-data';
}
```

Now the table listens for `reload-user-data` instead of `{tableKey}-refresh`.

### Custom Listeners

```php
public function listeners(): array
{
    return [
        'user-created'  => 'refreshTable',
        'order-updated' => 'handleOrderUpdate',
    ];
}

public function handleOrderUpdate(): void
{
    $this->resetPage();
}
```

Custom listeners are merged with the built-in ones.

---

## Filter Events

Every table automatically dispatches a `table-filters-applied` event whenever a filter is applied, removed, all filters are cleared, or the search term changes. This enables parent components (or any other Livewire component on the page) to react in real time to filter and search changes — for example, updating statistics cards, badges, or dependent UI.

### Event Payload

| Parameter | Type | Description |
|-----------|------|-------------|
| `tableKey` | `string` | The `$tableKey` of the table that changed |
| `filters` | `array` | Associative array of **currently active** filters (empty/null values are omitted) |
| `search` | `string` | The current search term (empty string when no search is active) |

The `filters` array is keyed by each filter's resolved key (either the custom `->key()` or the `fieldName` with dots replaced by underscores). Values vary by filter type:

| Filter Type | Example Value |
|-------------|---------------|
| Text | `'John'` |
| Select | `'active'` |
| Select (multi) | `['pending', 'shipped']` |
| Number | `'50'` |
| NumberRange | `['min' => '100', 'max' => '500']` |
| Date | `'2024-01-15'` |
| DateRange | `['from' => '2024-01-01', 'to' => '2024-12-31']` |
| MultiDate | `['2024-01-01', '2024-06-15']` |
| Boolean | `'1'` or `'0'` |

### When the Event Is Dispatched

The event fires in all filter and search mutation methods:

- `applyFilter()` — when a filter value is set or changed
- `removeFilter()` — when a specific filter is removed (the chip "x" button)
- `clearFilters()` — when all filters are cleared at once
- `updatedSearch()` — when the search input value changes
- `clearSearch()` — when the search is cleared

It also fires after `updatedTableFilters()`, which Livewire calls whenever a `wire:model` bound filter input changes.

### Listening From a Parent Component

```php
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardPage extends Component
{
    public array $activeFilters = [];

    public array $searchTerms = [];

    #[On('table-filters-applied')]
    public function onFiltersApplied(string $tableKey, array $filters, string $search = ''): void
    {
        $this->activeFilters[$tableKey] = $filters;
        $this->searchTerms[$tableKey] = $search;
    }

    public function render()
    {
        $query = Order::query();
        $filters = $this->activeFilters['orders'] ?? [];
        $search = $this->searchTerms['orders'] ?? '';

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_name', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['unit_price'])) {
            if (($filters['unit_price']['min'] ?? '') !== '') {
                $query->where('unit_price', '>=', (float) $filters['unit_price']['min']);
            }
            if (($filters['unit_price']['max'] ?? '') !== '') {
                $query->where('unit_price', '<=', (float) $filters['unit_price']['max']);
            }
        }

        return view('livewire.dashboard', [
            'totalOrders'     => $query->count(),
            'deliveredOrders' => (clone $query)->where('status', 'delivered')->count(),
            'revenue'         => (clone $query)->sum('total') ?? 0,
        ]);
    }
}
```

### Listening From Alpine.js

```html
<div x-data="{ filterCount: 0 }"
     @table-filters-applied.window="filterCount = Object.keys($event.detail.filters ?? {}).length">
    <span x-text="filterCount + ' filters active'"></span>
</div>
```

### Multiple Tables — Reacting Per Table

When you have multiple tables on the same page, use the `tableKey` to distinguish which table changed:

```php
#[On('table-filters-applied')]
public function onFiltersApplied(string $tableKey, array $filters, string $search = ''): void
{
    $this->activeFilters[$tableKey] = $filters;
    $this->searchTerms[$tableKey] = $search;
}

public function render()
{
    // Products table stats
    $productsQuery = Product::query();
    foreach ($this->activeFilters['products'] ?? [] as $key => $value) {
        // Apply each filter...
    }

    // Orders table stats
    $ordersQuery = Order::query();
    foreach ($this->activeFilters['orders'] ?? [] as $key => $value) {
        // Apply each filter...
    }

    return view('livewire.page', [
        'totalProducts' => $productsQuery->count(),
        'totalOrders'   => $ordersQuery->count(),
    ]);
}
```

### Full Example: Stats Cards That Update With Filters

**Table component:**

```php
class UserTable extends DataTableComponent
{
    public string $tableKey = 'users';

    public function filters(): array
    {
        return [
            Filter::make('department')
                ->select([
                    'engineering' => 'Engineering',
                    'sales'       => 'Sales',
                    'marketing'   => 'Marketing',
                ]),
            Filter::make('status')
                ->select([
                    'active'   => 'Active',
                    'inactive' => 'Inactive',
                ]),
            Filter::make('salary')
                ->numberRange()
                ->config(['min' => 0, 'max' => 200000]),
        ];
    }
}
```

**Parent component:**

```php
class TeamDashboard extends Component
{
    public array $activeFilters = [];

    public array $searchTerms = [];

    #[On('table-filters-applied')]
    public function onFiltersApplied(string $tableKey, array $filters, string $search = ''): void
    {
        $this->activeFilters[$tableKey] = $filters;
        $this->searchTerms[$tableKey] = $search;
    }

    public function render()
    {
        $query = User::query();
        $filters = $this->activeFilters['users'] ?? [];
        $search = $this->searchTerms['users'] ?? '';

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['salary'])) {
            if (($filters['salary']['min'] ?? '') !== '') {
                $query->where('salary', '>=', (float) $filters['salary']['min']);
            }
            if (($filters['salary']['max'] ?? '') !== '') {
                $query->where('salary', '<=', (float) $filters['salary']['max']);
            }
        }

        return view('livewire.team-dashboard', [
            'totalUsers'   => $query->count(),
            'activeUsers'  => (clone $query)->where('status', 'active')->count(),
            'avgSalary'    => $query->avg('salary') ?? 0,
        ]);
    }
}
```

**Blade template:**

```html
<div>
    <div class="stats-grid">
        <div class="stat-card">
            <span>Total Users</span>
            <strong>{{ $totalUsers }}</strong>
        </div>
        <div class="stat-card">
            <span>Active Users</span>
            <strong>{{ $activeUsers }}</strong>
        </div>
        <div class="stat-card">
            <span>Avg Salary</span>
            <strong>${{ number_format($avgSalary, 2) }}</strong>
        </div>
    </div>

    <livewire:user-table />
</div>
```

When the user selects "Engineering" in the department filter, the stats cards instantly update to show only engineering users. When they type in the search box, the stats reflect only the matching rows. When they remove a filter (chip "x") or clear the search, the stats go back to the broader dataset. When they clear all filters, the stats reset to the full dataset.

---

## Lifecycle Hooks

Override these methods to hook into the table's render cycle:

| Method | When | Receives |
|--------|------|----------|
| `onQuerying(Builder $query)` | Before pipeline processing | The raw Eloquent builder |
| `onQueried(LengthAwarePaginator $rows)` | After pipeline processing | Paginated results |
| `onRendering(array $viewData): array` | Before view render | View data array (must return it) |
| `onRendered()` | After view render | Nothing |

### Example: Modify Query Before Processing

```php
protected function onQuerying(Builder $query): void
{
    $query->where('tenant_id', auth()->user()->tenant_id);
}
```

### Example: Log Results

```php
protected function onQueried(LengthAwarePaginator $rows): void
{
    logger()->info("Table rendered with {$rows->total()} results");
}
```

### Example: Add Extra View Data

```php
protected function onRendering(array $viewData): array
{
    $viewData['summary'] = $this->calculateSummary($viewData['rows']);
    return $viewData;
}
```

### Example: Track Performance

```php
private float $startTime;

protected function onQuerying(Builder $query): void
{
    $this->startTime = microtime(true);
}

protected function onRendered(): void
{
    $elapsed = microtime(true) - $this->startTime;
    logger()->debug("Table render took {$elapsed}s");
}
```

## Livewire Event Dispatch

Enable automatic Livewire event dispatch for the table lifecycle:

```php
protected function shouldDispatchTableEvents(): bool
{
    return true;
}
```

This dispatches Livewire events that other components can listen to:

| Event | Dispatched |
|-------|-----------|
| `table-querying` | Before pipeline |
| `table-queried` | After pipeline |
| `table-rendering` | Before view render |
| `table-rendered` | After view render |

### Listen From Another Component

```php
#[On('table-queried')]
public function handleTableQueried(): void
{
    // React to table data changes
}
```

## Multiple Tables on One Page

Set unique `tableKey` values:

```php
class UserTable extends DataTableComponent
{
    public string $tableKey = 'users';
}

class OrderTable extends DataTableComponent
{
    public string $tableKey = 'orders';
}
```

Refresh them independently:

```php
$this->dispatch('users-refresh');   // only UserTable
$this->dispatch('orders-refresh');  // only OrderTable
$this->dispatch('livewire-tables-refresh'); // both
```

`refreshTable()` resets pagination to page 1 and triggers a re-render. Search, filters, and sorting are preserved.
