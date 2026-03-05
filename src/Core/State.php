<?php

declare(strict_types=1);

namespace Livewire\Tables\Core;

use Livewire\Tables\Core\Contracts\StateContract;

final class State implements StateContract
{
    public function __construct(
        private readonly string $search = '',
        private readonly array $sortFields = [],
        private readonly array $filters = [],
        private readonly int $perPage = 10,
        private readonly int $page = 1,
    ) {}

    public function search(): string
    {
        return $this->search;
    }

    public function sortFields(): array
    {
        return $this->sortFields;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function page(): int
    {
        return $this->page;
    }
}
