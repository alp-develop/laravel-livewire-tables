<?php

declare(strict_types=1);

namespace Livewire\Tables\Columns;

use Illuminate\Database\Eloquent\Model;

final class DateColumn extends Column
{
    private string $dateFormat = 'Y-m-d';

    public static function make(string $field): static
    {
        return new self($field);
    }

    public function type(): string
    {
        return 'date';
    }

    public function format(string|\Closure $format): static
    {
        if (is_string($format)) {
            $this->dateFormat = $format;

            return $this;
        }

        $this->formatCallback = $format;

        return $this;
    }

    public function resolveValue(Model $row): mixed
    {
        $value = data_get($row, $this->resolveKeyForRow($row));

        if ($this->formatCallback !== null) {
            return ($this->formatCallback)($value, $row);
        }

        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($this->dateFormat);
        }

        $timestamp = strtotime((string) $value);

        return $timestamp !== false ? date($this->dateFormat, $timestamp) : null;
    }
}
