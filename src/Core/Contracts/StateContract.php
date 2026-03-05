<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Contracts;

interface StateContract
{
    public function search(): string;

    /** @return array<string, string> */
    public function sortFields(): array;

    /** @return array<string, mixed> */
    public function filters(): array;

    public function perPage(): int;

    public function page(): int;
}
