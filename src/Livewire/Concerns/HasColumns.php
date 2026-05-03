<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Livewire\Tables\Core\Contracts\ColumnContract;

/**
 * @requires HasBulkActions  (deselectAll)
 * @requires \Livewire\WithPagination  (resetPage)
 */
trait HasColumns
{
    /** @var array<int, ColumnContract>|null */
    private ?array $cachedColumns = null;

    /** @var array<int, ColumnContract>|null */
    private ?array $cachedAllColumns = null;

    /** @var array<int, ColumnContract>|null */
    private ?array $cachedVisibleColumns = null;

    /** @var array<int, string> */
    public array $hiddenColumns = [];

    /** @return array<int, ColumnContract> */
    abstract public function columns(): array;

    /** @return array<int, ColumnContract> */
    protected function resolveColumns(): array
    {
        if ($this->cachedColumns === null) {
            $this->cachedColumns = $this->columns();
        }

        return $this->cachedColumns;
    }

    /** @return array<int, ColumnContract> */
    public function getAllColumns(): array
    {
        return $this->cachedAllColumns ??= array_values(array_filter(
            $this->resolveColumns(),
            fn (ColumnContract $column): bool => $column->isVisible() && ! $column->isHiddenIf(),
        ));
    }

    /** @return array<int, ColumnContract> */
    public function getVisibleColumns(): array
    {
        if ($this->cachedVisibleColumns === null) {
            $this->cachedVisibleColumns = array_values(array_filter(
                $this->resolveColumns(),
                fn (ColumnContract $column): bool => $column->isVisible()
                    && ! $column->isHiddenIf()
                    && ! in_array($column->field(), $this->hiddenColumns, true),
            ));
        }

        return $this->cachedVisibleColumns;
    }

    /** @var array<int, ColumnContract>|null */
    private ?array $cachedSearchableColumns = null;

    /** @return array<int, ColumnContract> */
    public function getSearchableColumns(): array
    {
        if ($this->cachedSearchableColumns === null) {
            $this->cachedSearchableColumns = array_values(array_filter(
                $this->resolveColumns(),
                fn (ColumnContract $column): bool => $column->isSearchable() && ! $column->isHiddenIf(),
            ));
        }

        return $this->cachedSearchableColumns;
    }

    public function toggleColumn(string $field): void
    {
        $validFields = array_map(fn ($c) => $c->field(), $this->resolveColumns());

        if (! in_array($field, $validFields, true)) {
            return;
        }

        if (in_array($field, $this->hiddenColumns, true)) {
            $this->hiddenColumns = array_values(array_diff($this->hiddenColumns, [$field]));
        } else {
            $this->hiddenColumns[] = $field;
        }
    }

    public function isColumnVisible(string $field): bool
    {
        return ! in_array($field, $this->hiddenColumns, true);
    }
}
