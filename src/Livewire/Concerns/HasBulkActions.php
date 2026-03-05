<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

trait HasBulkActions
{
    /** @var array<int, string> */
    public array $selectedIds = [];

    /** @var array<int, string> */
    public array $excludedIds = [];

    /** @var array<int, string> */
    public array $pageIds = [];

    public bool $selectAllPages = false;

    /** @return array<string, string> */
    public function bulkActions(): array
    {
        return [];
    }

    public function hasBulkActions(): bool
    {
        return count($this->bulkActions()) > 0;
    }

    public function toggleSelected(mixed $id): void
    {
        $id = (string) $id;

        if ($this->selectAllPages) {
            if (in_array($id, $this->excludedIds, true)) {
                $this->excludedIds = array_values(array_diff($this->excludedIds, [$id]));
            } else {
                $this->excludedIds[] = $id;
            }
        } else {
            if (in_array($id, $this->selectedIds, true)) {
                $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
            } else {
                $this->selectedIds[] = $id;
            }
        }
    }

    /** @param array<int, mixed> $ids */
    public function setPageSelection(array $ids, bool $select): void
    {
        $ids = array_map('strval', $ids);

        if ($this->selectAllPages) {
            if ($select) {
                $this->excludedIds = array_values(array_diff($this->excludedIds, $ids));
            } else {
                $this->excludedIds = array_values(array_unique(array_merge($this->excludedIds, $ids)));
            }
        } else {
            if ($select) {
                $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $ids)));
            } else {
                $this->selectedIds = array_values(array_diff($this->selectedIds, $ids));
            }
        }
    }

    public function selectAllAcrossPages(): void
    {
        $this->selectAllPages = true;
        $this->excludedIds = [];
        $this->selectedIds = [];
    }

    public function deselectAll(): void
    {
        $this->selectedIds = [];
        $this->excludedIds = [];
        $this->selectAllPages = false;
    }

    public function getSelectedCount(int $total): int
    {
        if ($this->selectAllPages) {
            return max(0, $total - count($this->excludedIds));
        }

        return count($this->selectedIds);
    }
}
