<?php

declare(strict_types=1);

namespace Livewire\Tables\Columns;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

final class BladeColumn extends Column
{
    private static int $bladeCounter = 0;

    public static function make(string $field = ''): static
    {
        if ($field === '') {
            $field = '_blade_'.self::$bladeCounter++;
        }

        $instance = new self($field);
        $instance->sortable = false;
        $instance->searchable = false;

        return $instance;
    }

    public static function resetCounter(): void
    {
        self::$bladeCounter = 0;
    }

    public function searchable(string|Closure|null $searchUsing = null): static
    {
        if ($searchUsing instanceof Closure) {
            $this->searchable = true;
            $this->searchCallback = $searchUsing;
        }

        return $this;
    }

    public function type(): string
    {
        return 'blade';
    }

    public function resolveValue(Model $row): mixed
    {
        return null;
    }

    public function renderCell(Model $row, mixed $table = null): string
    {
        if ($this->renderCallback === null) {
            return '';
        }

        $result = ($this->renderCallback)($row, $table);

        if ($result instanceof View) {
            return $result->render();
        }

        return (string) $result;
    }
}
