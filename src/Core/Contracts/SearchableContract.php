<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Contracts;

use Closure;

interface SearchableContract
{
    public function getSearchField(): ?string;

    public function getSearchCallback(): ?Closure;
}
