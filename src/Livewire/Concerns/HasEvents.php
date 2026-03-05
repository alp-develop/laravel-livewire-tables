<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

trait HasEvents
{
    protected function onQuerying(Builder $query): void {}

    protected function onQueried(LengthAwarePaginator $rows): void {}

    /**
     * @param  array<string, mixed>  $viewData
     * @return array<string, mixed>
     */
    protected function onRendering(array $viewData): array
    {
        return $viewData;
    }

    protected function onRendered(): void {}

    protected function shouldDispatchTableEvents(): bool
    {
        return false;
    }

    protected function fireTableEvent(string $event, mixed ...$params): void
    {
        if ($this->shouldDispatchTableEvents()) {
            $this->dispatch("table-{$event}", ...$params);
        }
    }
}
