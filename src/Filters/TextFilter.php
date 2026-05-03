<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

final class TextFilter extends Filter
{
    public function type(): string
    {
        return 'text';
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        $value = mb_substr((string) $value, 0, 200);

        $escaped = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);

        return $query->whereRaw("{$this->fieldName} LIKE ? ESCAPE '!'", ["%{$escaped}%"]);
    }
}
