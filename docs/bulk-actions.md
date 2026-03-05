# Bulk Actions

Bulk actions let users select rows and perform operations on them. The system uses an **exclusion model** for efficient handling of large datasets.

## Enabling Bulk Actions

Define the `bulkActions()` method in your table component:

```php
public function bulkActions(): array
{
    return [
        'deleteSelected' => 'Delete Selected',
        'exportSelected' => 'Export to CSV',
        'activateSelected' => 'Activate',
    ];
}
```

Each key is the method name to call, and the value is the display label.

## Implementing Actions

Define a public method for each action:

```php
public function deleteSelected(): void
{
    $ids = $this->getSelectedIds();
    User::whereIn('id', $ids)->delete();
}

public function activateSelected(): void
{
    $ids = $this->getSelectedIds();
    User::whereIn('id', $ids)->update(['active' => true]);
}
```

After execution, `deselectAll()` is called automatically.

## Auto CSV Export

The `HasExport` trait provides automatic CSV export from visible columns without manual mapping:

```php
public function bulkActions(): array
{
    return [
        'exportCsvAuto' => 'Export CSV',
    ];
}
```

That's it. `exportCsvAuto()` automatically:
- Uses visible column labels as CSV headers
- Resolves cell values via each column's `resolveValue()` method
- Excludes `blade`, `action`, and `image` column types
- Respects active search, filters, and sort
- When bulk selection is active, exports only selected rows
- Streams the download (memory-efficient for large datasets)

### Configuration

```php
public function configure(): void
{
    $this->setExportFilename('users');     // Default: 'export'
    $this->setExportChunkSize(1000);       // Default: 500
}
```

The filename is suffixed with the current date: `users-2024-12-15.csv`

### Custom Export Logic

For full control, build your own export method:

```php
use Symfony\Component\HttpFoundation\StreamedResponse;

public function exportSelected(): StreamedResponse
{
    $ids = $this->getSelectedIds();
    $users = User::whereIn('id', $ids)->get();

    return response()->streamDownload(function () use ($users) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Name', 'Email', 'Created']);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->created_at->format('Y-m-d'),
            ]);
        }

        fclose($handle);
    }, 'users-export.csv', ['Content-Type' => 'text/csv']);
}
```

## Selection Model

### Individual Selection (default)

Clicking individual checkboxes stores IDs in `$selectedIds`.

### Select All Pages (exclusion model)

When "Select All" is clicked, the system switches to **exclusion mode**:

- `selectAllPages = true`
- All rows are considered selected
- Unchecked rows are tracked in `$excludedIds`

This means "all rows EXCEPT the excluded ones" — efficient even with millions of rows.

### Getting Selected IDs

```php
$ids = $this->getSelectedIds();
```

- In individual mode: returns `$selectedIds`
- In exclusion mode: queries the database for all matching IDs minus `$excludedIds`, respecting active search and filters

The method uses the model's actual primary key (`getKeyName()`), so custom primary keys and UUIDs work correctly.

## Available Methods

| Method | Description |
|--------|-------------|
| `bulkActions()` | Define available actions |
| `getSelectedIds()` | Get resolved IDs (handles exclusion model) |
| `toggleSelected($id)` | Toggle a single row |
| `setPageSelection($ids, $select)` | Select/deselect all rows on current page |
| `selectAllAcrossPages()` | Enter exclusion mode (select everything) |
| `deselectAll()` | Clear all selections |
| `getSelectedCount($total)` | Get count of selected rows |
| `hasBulkActions()` | Check if any bulk actions are defined |
| `exportCsvAuto()` | Auto-generate CSV from visible columns |
| `setExportFilename(string)` | Set the export file name |
| `setExportChunkSize(int)` | Set the chunk size for streaming export |

## UI Behavior

- A checkbox column is automatically added when `bulkActions()` returns actions
- The bulk actions dropdown is disabled when no rows are selected
- A badge shows the count of selected rows
- "Select all across pages" banner appears when the current page is fully selected
- Active search and filters are respected when resolving IDs in exclusion mode
