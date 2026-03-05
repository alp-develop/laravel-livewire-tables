<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Contracts;

interface ThemeContract
{
    public function name(): string;

    /** @return array<string, string> */
    public function classes(): array;

    public function paginationView(): string;

    public function supportsImportantPrefix(): bool;
}
