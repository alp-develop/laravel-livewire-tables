# Toolbar Slots

Toolbar slots allow you to inject custom content into the table's toolbar area and around the table itself.

## Available Slots

| Method | Position |
|--------|----------|
| `toolbarLeftPrepend()` | Before search input (left side) |
| `toolbarLeftAppend()` | After search & filter buttons (left side) |
| `toolbarRightPrepend()` | Before bulk/columns/per-page (right side) |
| `toolbarRightAppend()` | After per-page selector (right side) |
| `beforeTable()` | After toolbar, before data rows |
| `afterTable()` | After data rows, before pagination |

Each method returns `View|string|null`. Return `null` (default) to render nothing.

## Usage

Override the slot methods in your table component:

```php
use Illuminate\Contracts\View\View;

class UsersTable extends DataTableComponent
{
    public function toolbarLeftPrepend(): View|string|null
    {
        return '<button wire:click="createUser" class="lt-btn-primary">+ New User</button>';
    }

    public function toolbarRightAppend(): View|string|null
    {
        return view('tables.partials.toolbar-actions');
    }

    public function beforeTable(): View|string|null
    {
        return '<div class="p-3 bg-yellow-50 text-yellow-800 rounded">⚠ 5 users pending approval</div>';
    }

    public function afterTable(): View|string|null
    {
        return view('tables.partials.summary', [
            'total' => $this->getSelectedCount($this->totalRows ?? 0),
        ]);
    }
}
```

## Returning a View

```php
public function toolbarLeftPrepend(): View|string|null
{
    return view('tables.partials.create-button', [
        'label' => 'New User',
        'action' => 'createUser',
    ]);
}
```

```blade
{{-- resources/views/tables/partials/create-button.blade.php --}}
<button wire:click="{{ $action }}" class="lt-btn-primary">
    {{ $label }}
</button>
```

## Returning HTML String

```php
public function toolbarRightPrepend(): View|string|null
{
    $count = User::where('active', false)->count();
    return "<span class='text-red-600 text-sm font-medium'>{$count} inactive</span>";
}
```

## Conditional Slots

```php
public function beforeTable(): View|string|null
{
    if (! $this->hasActiveFilters()) {
        return null;
    }

    return '<div class="p-2 text-sm text-gray-500">Filters are active. Showing filtered results.</div>';
}
```
