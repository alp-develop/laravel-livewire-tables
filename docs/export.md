# Export

The `HasExport` trait provides automatic CSV export from visible columns.

## Auto Export

The simplest way to add CSV export is as a bulk action:

```php
public function bulkActions(): array
{
    return [
        'exportCsvAuto' => 'Export CSV',
    ];
}
```

No additional code needed. The export:
- Uses visible column labels as CSV headers
- Resolves cell values via each column's `resolveValue()`
- Excludes `blade`, `action`, and `image` column types
- Respects active search, filters, and sort
- When bulk selection is active, exports only selected rows
- Streams the download (memory-efficient)

## Configuration

```php
public function configure(): void
{
    $this->setExportFilename('users');     // Default: 'export'
    $this->setExportChunkSize(1000);       // Default: 500
}
```

The filename is suffixed with the current date: `users-2024-12-15.csv`

## Direct Usage

Call `exportCsvAuto()` from any method (not just bulk actions):

```php
public function downloadReport(): StreamedResponse
{
    return $this->exportCsvAuto();
}
```

## Export Selected Only

When rows are bulk-selected, `exportCsvAuto()` exports only those rows. Otherwise it exports the full filtered/searched query.

The export uses the model's actual primary key (`getKeyName()`), so UUIDs and custom primary keys work correctly.

## Custom Export

For full control, build your own export method:

```php
use Symfony\Component\HttpFoundation\StreamedResponse;

public function exportCustom(): StreamedResponse
{
    $query = $this->buildExportQuery();

    return response()->streamDownload(function () use ($query) {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Name', 'Email']);

        $query->chunk(500, function ($rows) use ($out) {
            foreach ($rows as $row) {
                fputcsv($out, [$row->id, $row->name, $row->email]);
            }
        });

        fclose($out);
    }, 'custom-export.csv', ['Content-Type' => 'text/csv']);
}
```

`buildExportQuery()` returns a Builder with search, filter, and sort pipeline applied. If bulk selection is active, it filters to selected IDs.

## Available Methods

| Method | Description |
|--------|-------------|
| `exportCsvAuto()` | Auto-generate and stream CSV download |
| `buildExportQuery()` | Get the query with pipeline + selection applied |
| `setExportFilename(string)` | Set the export file name (no extension) |
| `setExportChunkSize(int)` | Set the chunk size for streaming |
