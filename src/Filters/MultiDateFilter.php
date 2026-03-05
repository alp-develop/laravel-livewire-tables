<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

final class MultiDateFilter extends Filter
{
    private string $dateFormat = 'Y-m-d';

    private ?string $minDateValue = null;

    private ?string $maxDateValue = null;

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

    public function type(): string
    {
        return 'multi_date';
    }

    public function normalizeValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        return array_values(array_filter($value, function (mixed $date): bool {
            if (! is_string($date) || $date === '') {
                return false;
            }

            if ($this->minDateValue !== null && $date < $this->minDateValue) {
                return false;
            }

            if ($this->maxDateValue !== null && $date > $this->maxDateValue) {
                return false;
            }

            return true;
        }));
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value) || count($value) === 0) {
            return $query;
        }

        $dates = array_filter($value, fn ($d) => is_string($d) && $d !== '');

        if (count($dates) === 0) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($dates): void {
            foreach ($dates as $date) {
                $q->orWhereDate($this->fieldName, $date);
            }
        });
    }
}
