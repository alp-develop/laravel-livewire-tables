<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

final class BooleanFilter extends Filter
{
    public function type(): string
    {
        return 'boolean';
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        return $query->where($this->fieldName, (bool) $value);
    }
}
