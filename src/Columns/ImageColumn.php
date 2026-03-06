<?php

declare(strict_types=1);

namespace Livewire\Tables\Columns;

use Illuminate\Database\Eloquent\Model;

final class ImageColumn extends Column
{
    private int $imageWidth = 50;

    private int $imageHeight = 50;

    private string $altField = '';

    public static function make(string $field): static
    {
        return new self($field);
    }

    public function type(): string
    {
        return 'image';
    }

    public function dimensions(int $width, int $height): static
    {
        $this->imageWidth = $width;
        $this->imageHeight = $height;

        return $this;
    }

    public function alt(string $field): static
    {
        $this->altField = $field;

        return $this;
    }

    public function getImageWidth(): int
    {
        return $this->imageWidth;
    }

    public function getImageHeight(): int
    {
        return $this->imageHeight;
    }

    public function getAltField(): string
    {
        return $this->altField;
    }

    public function resolveValue(Model $row): mixed
    {
        $value = data_get($row, $this->resolveKeyForRow($row));

        if ($this->formatCallback !== null) {
            return ($this->formatCallback)($value, $row);
        }

        return $value;
    }
}
