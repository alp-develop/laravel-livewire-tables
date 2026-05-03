<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

/**
 * @requires HasStateCache  (tableKey)
 * @requires \Livewire\WithPagination  (resetPage)
 */
trait HasListeners
{
    protected string $refreshEvent = '';

    /** @return array<string, string> */
    public function listeners(): array
    {
        return [];
    }

    public function getRefreshEventName(): string
    {
        if ($this->refreshEvent !== '') {
            return $this->refreshEvent;
        }

        $raw = $this->tableKey !== ''
            ? $this->tableKey
            : md5(static::class);

        return $raw.'-refresh';
    }

    /** @return array<string, string> */
    public function getListeners(): array
    {
        $defaults = [
            'livewire-tables-refresh' => 'refreshTable',
            $this->getRefreshEventName() => 'refreshTable',
        ];

        return array_merge($defaults, $this->listeners());
    }

    public function refreshTable(): void
    {
        $this->resetPage();
    }
}
