<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

/**
 * @requires HasColumns  (resolveColumns)
 * @requires \Livewire\WithPagination  (resetPage)
 */
trait HasSorting
{
    /** @var array<string, string> */
    public array $sortFields = [];

    public function sortBy(string $field): void
    {
        $sortableFields = array_map(
            fn ($c) => $c->field(),
            array_filter($this->resolveColumns(), fn ($c) => $c->isSortable()),
        );

        if (! in_array($field, $sortableFields, true)) {
            return;
        }

        if (array_key_exists($field, $this->sortFields)) {
            if ($this->sortFields[$field] === 'asc') {
                $this->sortFields[$field] = 'desc';
            } else {
                unset($this->sortFields[$field]);
            }
        } else {
            $this->sortFields[$field] = $this->defaultSortDirection;
        }

        $this->resetPage();
    }

    public function clearSort(): void
    {
        $this->sortFields = [];
        $this->resetPage();
    }

    public function clearSortField(string $field): void
    {
        unset($this->sortFields[$field]);
        $this->resetPage();
    }

    public function isSortedBy(string $field): bool
    {
        return array_key_exists($field, $this->sortFields);
    }

    public function getSortDirection(string $field): string
    {
        return $this->sortFields[$field] ?? 'asc';
    }

    public function getSortOrder(string $field): int
    {
        $keys = array_keys($this->sortFields);
        $pos = array_search($field, $keys, true);

        return $pos !== false ? (int) $pos + 1 : 0;
    }
}
