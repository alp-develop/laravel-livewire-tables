<?php

declare(strict_types=1);

namespace Livewire\Tables\Core;

use Composer\InstalledVersions;
use Livewire\LivewireManager;

final class Compat
{
    private static ?int $livewireMajor = null;

    private static ?int $laravelMajor = null;

    public static function livewireVersion(): int
    {
        if (self::$livewireMajor !== null) {
            return self::$livewireMajor;
        }

        if (property_exists(LivewireManager::class, 'v4')) {
            return self::$livewireMajor = 4;
        }

        $raw = InstalledVersions::getPrettyVersion('livewire/livewire') ?? '3.0.0';

        return self::$livewireMajor = (int) ltrim(explode('.', $raw)[0], 'v');
    }

    public static function laravelVersion(): int
    {
        if (self::$laravelMajor !== null) {
            return self::$laravelMajor;
        }

        $raw = InstalledVersions::getPrettyVersion('laravel/framework')
            ?? InstalledVersions::getPrettyVersion('illuminate/support')
            ?? '12.0.0';

        return self::$laravelMajor = (int) ltrim(explode('.', $raw)[0], 'v');
    }

    public static function isLivewire3(): bool
    {
        return self::livewireVersion() === 3;
    }

    public static function isLivewire4(): bool
    {
        return self::livewireVersion() >= 4;
    }

    public static function isLaravel10(): bool
    {
        return self::laravelVersion() === 10;
    }

    public static function isLaravel11(): bool
    {
        return self::laravelVersion() === 11;
    }

    public static function isLaravel12(): bool
    {
        return self::laravelVersion() === 12;
    }

    public static function isLaravel13(): bool
    {
        return self::laravelVersion() >= 13;
    }

    public static function supports(string ...$features): bool
    {
        foreach ($features as $feature) {
            if (! self::checkFeature($feature)) {
                return false;
            }
        }

        return true;
    }

    private static function checkFeature(string $feature): bool
    {
        return match ($feature) {
            'reactive-props',
            'on-attribute',
            'locked-props',
            'computed' => true,

            'computed-persist',
            'session-props',
            'isolate',
            'async',
            'wire-sort' => self::isLivewire4(),

            default => false,
        };
    }

    public static function flush(): void
    {
        self::$livewireMajor = null;
        self::$laravelMajor = null;
    }
}
