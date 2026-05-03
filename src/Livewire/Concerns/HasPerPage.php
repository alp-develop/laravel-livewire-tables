<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

/**
 * @requires HasConfiguration  (defaultPerPage, perPageOptions)
 * @requires HasBulkActions    (deselectAll)
 * @requires \Livewire\WithPagination  (resetPage)
 */
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
