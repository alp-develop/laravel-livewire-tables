<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Livewire\Tables\Themes\ThemeManager;
use Livewire\WithPagination;

trait HasPagination
{
    use WithPagination;

    public function paginationView(): string
    {
        return app(ThemeManager::class)->resolve()->paginationView();
    }
}
