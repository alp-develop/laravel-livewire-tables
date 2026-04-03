<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Pipeline;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\SearchableContract;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;

final class SearchStep implements StepContract
{
    public function __construct(
        private readonly array $columns = [],
    ) {}

    public function apply(Builder $query, StateContract $state): Builder
    {
        $search = trim($state->search());

        if ($search === '') {
            return $query;
        }

        $searchableColumns = array_filter(
            $this->columns,
            fn (ColumnContract $column): bool => $column->isSearchable(),
        );

        if (count($searchableColumns) === 0) {
            return $query;
        }

        $aliasMap = $this->buildAliasMap($query);

        return $query->where(function (Builder $query) use ($searchableColumns, $search, $aliasMap): void {
            foreach ($searchableColumns as $column) {
                if ($column instanceof SearchableContract) {
                    $callback = $column->getSearchCallback();

                    if ($callback !== null) {
                        $callback($query, $search);

                        continue;
                    }

                    $searchField = $column->getSearchField();

                    if ($searchField !== null) {
                        $this->applyFieldSearch($query, $searchField, $search);

                        continue;
                    }
                }

                $field = $aliasMap[$column->field()] ?? $column->field();
                $this->applyFieldSearch($query, $field, $search);
            }
        });
    }

    private function buildAliasMap(Builder $query): array
    {
        $map = [];
        $columns = $query->getQuery()->columns ?? [];

        foreach ($columns as $column) {
            if (! is_string($column)) {
                continue;
            }

            if (preg_match('/^(.+)\s+as\s+(\w+)$/i', trim($column), $matches)) {
                $map[$matches[2]] = $matches[1];
            }
        }

        return $map;
    }

    private function applyFieldSearch(Builder $query, string $field, string $search): void
    {
        $safeField = preg_replace('/[^a-zA-Z0-9_.]/', '', $field);

        if ($safeField === null || $safeField === '') {
            return;
        }

        $query->orWhere($safeField, 'LIKE', "%{$search}%");
    }
}
