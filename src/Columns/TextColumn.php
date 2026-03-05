<?php

declare(strict_types=1);

namespace Livewire\Tables\Columns;

final class TextColumn extends Column
{
    private static int $textCounter = 0;

    public static function make(string $field = ''): static
    {
        if ($field === '') {
            $field = '_text_'.self::$textCounter++;
        }

        return new self($field);
    }

    public static function resetCounter(): void
    {
        self::$textCounter = 0;
    }
}
