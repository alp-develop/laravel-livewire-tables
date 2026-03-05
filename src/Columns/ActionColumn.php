<?php

declare(strict_types=1);

namespace Livewire\Tables\Columns;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

final class ActionColumn extends Column
{
    private static int $actionCounter = 0;

    /** @var array<int, array{label: string, icon: string|null, class: string, action: Closure, visible: Closure|bool}> */
    private array $actions = [];

    public static function make(string $field = ''): static
    {
        if ($field === '') {
            $field = '_actions_'.self::$actionCounter++;
        }

        $instance = new self($field);
        $instance->sortable = false;
        $instance->searchable = false;
        $instance->labelText = 'Actions';

        return $instance;
    }

    public static function resetCounter(): void
    {
        self::$actionCounter = 0;
    }

    public function button(
        string $label,
        Closure $action,
        string $class = '',
        ?string $icon = null,
        Closure|bool $visible = true,
    ): static {
        $this->actions[] = [
            'label' => $label,
            'icon' => $icon,
            'class' => $class,
            'action' => $action,
            'visible' => $visible,
        ];

        return $this;
    }

    /** @return array<int, array{label: string, icon: string|null, class: string, action: Closure, visible: Closure|bool}> */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function searchable(string|Closure|null $searchUsing = null): static
    {
        return $this;
    }

    public function type(): string
    {
        return 'action';
    }

    public function resolveValue(Model $row): mixed
    {
        return null;
    }

    public function renderCell(Model $row, mixed $table = null): string
    {
        if ($this->renderCallback !== null) {
            $result = ($this->renderCallback)($row, $table);

            return $result instanceof View ? $result->render() : (string) $result;
        }

        $html = '<div style="display:flex;gap:0.25rem;align-items:center;flex-wrap:wrap">';

        foreach ($this->actions as $action) {
            $isVisible = $action['visible'] instanceof Closure
                ? ($action['visible'])($row)
                : $action['visible'];

            if (! $isVisible) {
                continue;
            }

            $wireAction = ($action['action'])($row, $table);
            $icon = $action['icon'] ?? '';
            $class = $action['class'];
            $label = e($action['label']);

            $html .= "<button type=\"button\" wire:click=\"{$wireAction}\" class=\"{$class}\">{$icon}{$label}</button>";
        }

        $html .= '</div>';

        return $html;
    }
}
