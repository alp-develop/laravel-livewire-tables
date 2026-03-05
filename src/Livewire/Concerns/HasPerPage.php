<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

trait HasPerPage
{
    public int $perPage = 10;

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, $this->perPageOptions, true)) {
            $this->perPage = $this->defaultPerPage;
        }

        $this->resetPage();
    }
}
