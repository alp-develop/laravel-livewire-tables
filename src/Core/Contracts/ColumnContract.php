<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ColumnContract
{
    public function field(): string;

    public function getKey(): string;

    public function getLabel(): string;

    public function isSortable(): bool;

    public function isSearchable(): bool;

    public function isVisible(): bool;

    public function isHiddenIf(): bool;

    public function getSelectAlias(): ?string;

    public function getHeaderClass(): string;

    public function getCellClass(): string;

    public function type(): string;

    public function resolveValue(Model $row): mixed;
}
