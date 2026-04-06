<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Livewire\Attributes\Locked;

trait HasStateCache
{
    #[Locked]
    public string $tableKey = '';

    public function getTableKey(): string
    {
        if ($this->tableKey !== '') {
            return 'lwt_'.$this->tableKey;
        }

        return 'lwt_'.substr(md5(static::class), 0, 12);
    }

    protected function loadStateFromCache(): void
    {
        $state = session('livewire_tables.'.$this->getTableKey(), []);

        if (! is_array($state) || empty($state)) {
            return;
        }

        if (isset($state['search']) && is_string($state['search'])) {
            $this->search = $state['search'];
        }

        if (isset($state['sortFields']) && is_array($state['sortFields'])) {
            $validSortableFields = array_map(
                fn ($c) => $c->field(),
                array_filter($this->resolveColumns(), fn ($c) => $c->isSortable()),
            );
            $this->sortFields = array_intersect_key(
                $state['sortFields'],
                array_flip($validSortableFields),
            );
            $this->sortFields = array_map(
                fn (mixed $dir): string => is_string($dir) && strtolower($dir) === 'desc' ? 'desc' : 'asc',
                $this->sortFields,
            );
        }

        if (isset($state['tableFilters']) && is_array($state['tableFilters'])) {
            $validFilterKeys = array_map(fn ($f) => $f->getKey(), $this->filters());
            $this->tableFilters = array_intersect_key(
                $state['tableFilters'],
                array_flip($validFilterKeys),
            );
        }

        if (isset($state['perPage']) && is_int($state['perPage'])) {
            $this->perPage = $state['perPage'];
        }

        if (isset($state['hiddenColumns']) && is_array($state['hiddenColumns'])) {
            $validColumnFields = array_map(fn ($c) => $c->field(), $this->resolveColumns());
            $this->hiddenColumns = array_values(array_intersect(
                array_filter($state['hiddenColumns'], 'is_string'),
                $validColumnFields,
            ));
        }
    }

    public function dehydrate(): void
    {
        $validSortFields = array_intersect_key(
            $this->sortFields,
            array_flip(array_map(
                fn ($c) => $c->field(),
                array_filter($this->resolveColumns(), fn ($c) => $c->isSortable()),
            )),
        );

        $validFilterKeys = array_map(fn ($f) => $f->getKey(), $this->filters());
        $validFilters = array_intersect_key(
            $this->tableFilters,
            array_flip($validFilterKeys),
        );

        $validColumnFields = array_map(fn ($c) => $c->field(), $this->resolveColumns());
        $validHidden = array_values(array_intersect($this->hiddenColumns, $validColumnFields));

        session([
            'livewire_tables.'.$this->getTableKey() => [
                'search' => $this->search,
                'sortFields' => $validSortFields,
                'tableFilters' => $validFilters,
                'perPage' => $this->perPage,
                'hiddenColumns' => $validHidden,
            ],
        ]);
    }

    public function clearStateCache(): void
    {
        session()->forget('livewire_tables.'.$this->getTableKey());
    }
}
