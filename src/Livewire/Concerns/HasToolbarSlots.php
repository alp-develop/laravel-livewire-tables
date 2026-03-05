<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Illuminate\Contracts\View\View;

trait HasToolbarSlots
{
    public function toolbarLeftPrepend(): View|string|null
    {
        return null;
    }

    public function toolbarLeftAppend(): View|string|null
    {
        return null;
    }

    public function toolbarRightPrepend(): View|string|null
    {
        return null;
    }

    public function toolbarRightAppend(): View|string|null
    {
        return null;
    }

    public function beforeTable(): View|string|null
    {
        return null;
    }

    public function afterTable(): View|string|null
    {
        return null;
    }

    public function resolveSlot(View|string|null $slot): string
    {
        if ($slot === null) {
            return '';
        }

        if ($slot instanceof View) {
            return $slot->render();
        }

        return $slot;
    }
}
