<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterContract
{
    public function field(): string;

    public function getKey(): string;

    public function getLabel(): string;

    public function type(): string;

    public function apply(Builder $query, mixed $value): Builder;

    /** @return array<string, string> */
    public function options(): array;

    public function defaultValue(): mixed;

    public function hasFilter(): bool;

    public function applyFilter(Builder $query, mixed $value): Builder;

    public function run(Builder $query, mixed $value): Builder;

    public function hasInitialValue(): bool;

    public function getInitialValue(): mixed;

    public function normalizeValue(mixed $value): mixed;
}
