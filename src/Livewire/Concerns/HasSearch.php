<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

trait HasSearch
{
    public string $search = '';

    public function updatedSearch(): void
    {
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
