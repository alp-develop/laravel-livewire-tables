<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Livewire\Tables\Core\Contracts\FilterContract;
use Livewire\Tables\Filters\SelectFilter;

/**
 * @requires HasBulkActions  (deselectAll)
 * @requires \Livewire\WithPagination  (resetPage)
 * @requires HasEvents  (dispatchFiltersChanged)
 * @requires HasColumns  (resolveColumns)
 */
trait HasFilters
{
    /** @var array<string, mixed> */
    public array $tableFilters = [];

    /** @var array<int, FilterContract>|null */
    private ?array $cachedFilters = null;

    /** @return array<int, FilterContract> */
    protected function resolveFilters(): array
    {
        if ($this->cachedFilters === null) {
            $this->cachedFilters = $this->filters();
        }

        return $this->cachedFilters;
    }

    public function applyFilter(string $field, mixed $value): void
    {
        $validKeys = array_map(fn ($f) => $f->getKey(), $this->resolveFilters());

        if (! in_array($field, $validKeys, true)) {
            return;
        }

        $this->tableFilters[$field] = $value;
        $this->deselectAll();
        $this->resetPage();
        $this->dispatchFiltersChanged();
    }

    public function removeFilter(string $field): void
    {
        $validKeys = array_map(fn ($f) => $f->getKey(), $this->resolveFilters());

        if (! in_array($field, $validKeys, true)) {
            return;
        }

        $filter = null;
        foreach ($this->resolveFilters() as $f) {
            if ($f->getKey() === $field) {
                $filter = $f;
                break;
            }
        }

        $this->tableFilters[$field] = match ($filter?->type()) {
            'multi_select', 'multi_date' => [],
            'date_range' => ['from' => '', 'to' => ''],
            'number_range' => ['min' => '', 'max' => ''],
            default => '',
        };

        foreach ($this->resolveFilters() as $f) {
            if ($f instanceof SelectFilter && $f->hasDependency() && $f->getParent() === $field) {
                $this->tableFilters[$f->getKey()] = '';
            }
        }

        $this->deselectAll();
        $this->dispatch('remove-filter', field: $field);
        $this->resetPage();
        $this->dispatchFiltersChanged();
    }

    public function clearFilters(): void
    {
        $resetValues = [];
        foreach ($this->resolveFilters() as $filter) {
            $resetValues[$filter->getKey()] = match ($filter->type()) {
                'multi_select', 'multi_date' => [],
                'date_range' => ['from' => '', 'to' => ''],
                'number_range' => ['min' => '', 'max' => ''],
                default => '',
            };
        }
        $this->tableFilters = $resetValues;
        $this->deselectAll();
        $this->dispatch('livewire-tables:clear-filters');
        $this->resetPage();
        $this->dispatchFiltersChanged();
    }

    protected function dispatchFiltersChanged(): void
    {
        $this->dispatch('table-filters-applied',
            tableKey: $this->tableKey,
            filters: $this->getAppliedFilters(),
            search: trim($this->search),
        );
    }

    public function hasActiveFilters(): bool
    {
        return count(array_filter($this->tableFilters, function (mixed $value): bool {
            if (is_array($value)) {
                return count(array_filter($value, fn ($v) => $v !== null && $v !== '')) > 0;
            }

            return $value !== null && $value !== '';
        })) > 0;
    }

    protected function filterHasActiveValue(string $key): bool
    {
        if (! array_key_exists($key, $this->tableFilters)) {
            return false;
        }

        $value = $this->tableFilters[$key];

        if (is_array($value)) {
            return count(array_filter($value, fn ($v) => $v !== null && $v !== '')) > 0;
        }

        return $value !== null && $value !== '';
    }

    public function getFilterValue(string $field): mixed
    {
        return $this->tableFilters[$field] ?? null;
    }
}
