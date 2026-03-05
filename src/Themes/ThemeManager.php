<?php

declare(strict_types=1);

namespace Livewire\Tables\Themes;

use Livewire\Tables\Core\Contracts\ThemeContract;

final class ThemeManager
{
    /** @var array<string, class-string<ThemeContract>> */
    private array $themes = [];

    private ?ThemeContract $resolved = null;

    public function __construct(
        private string $active = 'tailwind',
    ) {
        $this->register('tailwind', TailwindTheme::class);
        $this->register('bootstrap5', Bootstrap5Theme::class);
        $this->register('bootstrap4', Bootstrap4Theme::class);
        $this->register('bootstrap-5', Bootstrap5Theme::class); // hyphenated alias
        $this->register('bootstrap-4', Bootstrap4Theme::class); // hyphenated alias
        $this->register('bootstrap', Bootstrap5Theme::class);  // backward compat alias
    }

    /** @param class-string<ThemeContract> $themeClass */
    public function register(string $name, string $themeClass): void
    {
        $this->themes[$name] = $themeClass;
        $this->resolved = null;
    }

    public function use(string $name): void
    {
        $this->active = $name;
        $this->resolved = null;
    }

    public function resolve(): ThemeContract
    {
        if ($this->resolved !== null) {
            return $this->resolved;
        }

        $themeClass = $this->themes[$this->active] ?? $this->themes['tailwind'];

        $this->resolved = new $themeClass;

        return $this->resolved;
    }

    public function active(): string
    {
        return $this->active;
    }

    /** @return array<string, class-string<ThemeContract>> */
    public function registered(): array
    {
        return $this->themes;
    }
}
