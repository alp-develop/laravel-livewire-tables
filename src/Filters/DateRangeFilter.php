<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class DateRangeFilter extends Filter
{
    private string $dateFormat = 'Y-m-d';

    private ?string $minDateValue = null;

    private ?string $maxDateValue = null;

    private ?string $calendarClassValue = null;

    public function format(string $format): static
    {
        $this->dateFormat = $format;

        return $this;
    }

    public function minDate(string $date): static
    {
        $this->minDateValue = $date;

        return $this;
    }

    public function maxDate(string $date): static
    {
        $this->maxDateValue = $date;

        return $this;
    }

    public function calendarClass(string $class): static
    {
        $this->calendarClassValue = $class;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->dateFormat;
    }

    public function getMinDate(): ?string
    {
        return $this->minDateValue;
    }

    public function getMaxDate(): ?string
    {
        return $this->maxDateValue;
    }

    public function getCalendarClass(): ?string
    {
        return $this->calendarClassValue;
    }

    public function type(): string
    {
        return 'date_range';
    }

    public function normalizeValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        foreach (['from', 'to'] as $key) {
            if (! isset($value[$key]) || $value[$key] === '') {
                continue;
            }

            if ($this->minDateValue !== null && $value[$key] < $this->minDateValue) {
                $value[$key] = '';
            } elseif ($this->maxDateValue !== null && $value[$key] > $this->maxDateValue) {
                $value[$key] = '';
            }
        }

        return $value;
    }

    public function applyFilter(Builder $query, mixed $value): Builder
    {
        return parent::applyFilter($query, $this->clampRange($value));
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value)) {
            return $query;
        }

        $value = $this->clampRange($value);
        $from = $value['from'] ?? null;
        $to = $value['to'] ?? null;

        if ($from !== null && $from !== '') {
            $query->whereDate($this->fieldName, '>=', $from);
        }

        if ($to !== null && $to !== '') {
            $query->whereDate($this->fieldName, '<=', $to);
        }

        return $query;
    }

    private function clampRange(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        foreach (['from', 'to'] as $key) {
            if (! isset($value[$key]) || $value[$key] === '') {
                continue;
            }

            $parsed = $this->dateFormat !== 'Y-m-d'
                ? Carbon::createFromFormat($this->dateFormat, $value[$key])
                : null;
            $normalized = ($parsed instanceof Carbon) ? $parsed->format('Y-m-d') : $value[$key];

            if ($this->minDateValue !== null && $normalized < $this->minDateValue) {
                $normalized = $this->minDateValue;
            }

            if ($this->maxDateValue !== null && $normalized > $this->maxDateValue) {
                $normalized = $this->maxDateValue;
            }

            $value[$key] = $normalized;
        }

        return $value;
    }
}
