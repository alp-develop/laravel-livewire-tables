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
    /** @var array<int, ColumnContract> */
    private readonly array $searchableColumns;

    /** @var array<string, string>|null */
    private ?array $cachedAliasMap = null;

    public function __construct(array $columns = [])
    {
        $this->searchableColumns = array_values(array_filter(
            $columns,
            fn (ColumnContract $column): bool => $column->isSearchable(),
        ));
    }

    public function apply(Builder $query, StateContract $state): Builder
    {
        $search = mb_substr(trim($state->search()), 0, 200);

        if ($search === '') {
            return $query;
        }

        if (count($this->searchableColumns) === 0) {
            return $query;
        }

        $aliasMap = $this->cachedAliasMap ??= $this->buildAliasMap($query);

        return $query->where(function (Builder $query) use ($search, $aliasMap): void {
            foreach ($this->searchableColumns as $column) {
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

        $escaped = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);

        $query->orWhereRaw("{$safeField} LIKE ? ESCAPE '!'", ["%{$escaped}%"]);
    }
}
