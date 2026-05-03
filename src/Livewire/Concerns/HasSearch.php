<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

/**
 * @requires HasBulkActions  (deselectAll)
 * @requires \Livewire\WithPagination  (resetPage)
 * @requires HasEvents  (dispatchFiltersChanged)
 */
trait HasSearch
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr($this->search, 0, 200);
        $this->deselectAll();
        $this->resetPage();
        $this->dispatchFiltersChanged();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->deselectAll();
        $this->resetPage();
        $this->dispatchFiltersChanged();
    }

    public function hasSearch(): bool
    {
        return trim($this->search) !== '';
    }
}
