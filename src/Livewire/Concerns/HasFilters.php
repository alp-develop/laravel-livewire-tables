<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

trait HasFilters
{
    /** @var array<string, mixed> */
    public array $tableFilters = [];

    public function applyFilter(string $field, mixed $value): void
    {
        $validKeys = array_map(fn ($f) => $f->getKey(), $this->filters());

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
        $validKeys = array_map(fn ($f) => $f->getKey(), $this->filters());

        if (! in_array($field, $validKeys, true)) {
            return;
        }

        unset($this->tableFilters[$field]);
        $this->deselectAll();
        $this->dispatch('remove-filter', field: $field);
        $this->resetPage();
        $this->dispatchFiltersChanged();
    }

    public function clearFilters(): void
    {
        $this->tableFilters = [];
        $this->deselectAll();
        $this->dispatch('livewire-tables:clear-filters');
        $this->resetPage();
        $this->dispatchFiltersChanged();
    }

    protected function dispatchFiltersChanged(): void
    {
        $this->dispatch('table-filters-applied',
            tableKey: $this->tableKey ?? '',
            filters: $this->getAppliedFilters(),
            search: trim($this->search ?? ''),
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

    public function getFilterValue(string $field): mixed
    {
        return $this->tableFilters[$field] ?? null;
    }
}
