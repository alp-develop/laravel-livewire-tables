<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Pipeline;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;

final class SortStep implements StepContract
{
    /** @var array<string, ColumnContract> */
    private readonly array $columnMap;

    public function __construct(
        private readonly array $columns = [],
    ) {
        $map = [];
        foreach ($this->columns as $column) {
            if ($column->isSortable()) {
                $map[$column->field()] = $column;
            }
        }
        $this->columnMap = $map;
    }

    public function apply(Builder $query, StateContract $state): Builder
    {
        $sortFields = $state->sortFields();

        if (count($sortFields) === 0) {
            return $query;
        }

        foreach ($sortFields as $field => $direction) {
            if (! isset($this->columnMap[$field])) {
                continue;
            }

            $safeField = preg_replace('/[^a-zA-Z0-9_.]/', '', $field);

            if ($safeField === null || $safeField === '' || $safeField !== $field) {
                continue;
            }

            $dir = strtolower($direction) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($safeField, $dir);
        }

        return $query;
    }
}
