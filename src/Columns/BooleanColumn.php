<?php

declare(strict_types=1);

namespace Livewire\Tables\Columns;

use Illuminate\Database\Eloquent\Model;

final class BooleanColumn extends Column
{
    private string $trueLabel = 'Yes';

    private string $falseLabel = 'No';

    public static function make(string $field): static
    {
        return new self($field);
    }

    public function type(): string
    {
        return 'boolean';
    }

    public function labels(string $trueLabel, string $falseLabel): static
    {
        $this->trueLabel = $trueLabel;
        $this->falseLabel = $falseLabel;

        return $this;
    }

    public function resolveValue(Model $row): mixed
    {
        if ($this->renderCallback !== null) {
            return ($this->renderCallback)($row);
        }

        $value = data_get($row, $this->resolutionKey());

        if ($this->formatCallback !== null) {
            return ($this->formatCallback)($value, $row);
        }

        return $value ? $this->trueLabel : $this->falseLabel;
    }
}
