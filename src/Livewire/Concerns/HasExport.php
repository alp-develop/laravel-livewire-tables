<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Pipeline\FilterStep;
use Livewire\Tables\Core\Pipeline\SearchStep;
use Livewire\Tables\Core\Pipeline\SortStep;
use Livewire\Tables\Core\State;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasExport
{
    protected string $exportFilename = 'export';

    protected int $exportChunkSize = 500;

    protected function setExportFilename(string $filename): static
    {
        $this->exportFilename = $filename;

        return $this;
    }

    protected function setExportChunkSize(int $size): static
    {
        $this->exportChunkSize = $size;

        return $this;
    }

    public function exportCsvAuto(): StreamedResponse
    {
        $columns = $this->getVisibleColumns();
        $exportable = array_filter($columns, fn ($col) => ! in_array($col->type(), ['blade', 'action', 'image'], true));

        $headers = array_map(fn ($col) => $col->getLabel(), $exportable);

        $query = $this->buildExportQuery();

        $filename = $this->exportFilename.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query, $exportable, $headers): void {
            $out = fopen('php://output', 'w');

            if ($out === false) {
                return;
            }

            fputcsv($out, $headers);

            $query->chunk($this->exportChunkSize, function ($rows) use ($out, $exportable): void {
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($exportable as $col) {
                        $value = $col->resolveValue($row);
                        $values[] = is_bool($value)
                            ? ($value ? 'Yes' : 'No')
                            : $this->escapeCsvValue((string) ($value ?? ''));
                    }
                    fputcsv($out, $values);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function buildExportQuery(): Builder
    {
        $columns = $this->resolveColumns();
        $filters = $this->filters();
        $state = new State(search: $this->search, filters: $this->tableFilters);

        $query = $this->query();
        $query = (new SearchStep($columns))->apply($query, $state);
        $query = (new FilterStep($filters))->apply($query, $state);
        $query = (new SortStep($columns))->apply($query, $state);

        if ($this->selectAllPages || count($this->selectedIds) > 0) {
            $ids = $this->getSelectedIds();
            if (count($ids) > 0) {
                $keyName = $query->getModel()->getKeyName();
                $query->whereIn($keyName, $ids);
            }
        }

        return $query;
    }

    private function escapeCsvValue(string $value): string
    {
        if ($value !== '' && str_contains("=+-@\t\r", $value[0])) {
            return "'".$value;
        }

        return $value;
    }
}
