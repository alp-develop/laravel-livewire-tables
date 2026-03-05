<?php

declare(strict_types=1);

namespace Livewire\Tables\Themes;

use Livewire\Tables\Core\Contracts\ThemeContract;

final class TailwindTheme implements ThemeContract
{
    public function name(): string
    {
        return 'tailwind';
    }

    public function paginationView(): string
    {
        return 'livewire-tables::components.pagination.tailwind';
    }

    public function supportsImportantPrefix(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function classes(): array
    {
        return [
            'container' => 'bg-white rounded-xl shadow-sm border border-gray-200',
            'toolbar' => 'px-4 py-3 border-b border-gray-100',
            'toolbar-row' => 'flex flex-col gap-2 sm:flex-row sm:items-center',
            'toolbar-left' => 'flex flex-col gap-2 w-full sm:flex-row sm:items-center sm:gap-2 sm:flex-1',
            'toolbar-right' => 'flex flex-col gap-2 w-full sm:flex-row sm:items-center sm:gap-2 sm:w-auto',
            'toolbar-search' => 'w-full sm:w-auto sm:flex-1 sm:max-w-[17rem]',
            'toolbar-item' => 'relative w-full sm:w-auto',
            'toolbar-btn-text' => '',
            'wrapper' => 'overflow-x-auto transition-opacity duration-200',
            'table' => 'min-w-full divide-y divide-gray-200',
            'thead' => 'bg-gray-50/80',
            'th' => 'px-4 py-3 text-left text-sm font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap',
            'th-sortable' => 'cursor-pointer select-none hover:text-gray-900 transition-colors',
            'th-sorted' => 'text-gray-900',
            'tbody' => 'bg-white divide-y divide-gray-100',
            'tr' => 'hover:bg-gray-50/60 transition-colors',
            'td' => 'px-4 py-3 text-base text-gray-700 whitespace-nowrap',
            'search-wrapper' => 'relative',
            'search-input' => 'lt-input block w-full rounded-lg border border-gray-300 bg-gray-50 py-2 pl-9 pr-3 text-sm placeholder-gray-400 focus:bg-white focus:outline-none transition-all',
            'search-icon' => 'pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400',
            'filter-btn' => 'w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 lt-hover-theme transition-colors',
            'filter-btn-active' => 'w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-lg border lt-border-400 lt-bg-50 px-3 py-2 text-sm font-medium lt-text-700 transition-colors',
            'filter-badge' => 'inline-flex items-center justify-center rounded-full lt-bg-600 px-1.5 text-[10px] font-bold text-white min-w-[18px] leading-[18px]',
            'filter-dropdown' => 'absolute left-0 z-50 mt-2 overflow-y-auto rounded-lg bg-white shadow-lg ring-1 ring-gray-200 p-4',
            'filter-wrapper' => 'space-y-3',
            'filter-group' => 'space-y-1',
            'filter-select' => 'lt-select w-full rounded-lg border border-gray-300 bg-gray-50 py-1.5 pl-3 pr-10 text-sm focus:outline-none',
            'filter-input' => 'lt-input w-full rounded-lg border border-gray-300 bg-gray-50 py-1.5 px-3 text-sm focus:outline-none',
            'filter-label' => 'block text-xs font-medium text-gray-500 uppercase tracking-wide',
            'filter-range-row' => 'flex items-center gap-2',
            'filter-range-separator' => 'text-gray-400 text-sm flex-shrink-0',
            'column-btn' => 'w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 lt-hover-theme transition-colors',
            'column-dropdown' => 'absolute left-0 sm:left-auto sm:right-0 z-50 mt-2 overflow-y-auto rounded-lg bg-white shadow-lg ring-1 ring-gray-200 p-3',
            'column-item' => 'flex items-center gap-2 py-1 px-1 rounded cursor-pointer hover:bg-gray-50',
            'column-checkbox' => 'lt-checkbox rounded border-gray-300 h-4 w-4',
            'column-item-label' => 'text-sm font-normal text-gray-700 select-none cursor-pointer',
            'chip-bar' => 'flex flex-wrap items-center gap-2 px-4 py-2.5 border-b border-gray-200',
            'chip' => 'inline-flex items-center gap-1 rounded-full lt-bg-50 px-2.5 py-1 text-xs font-medium lt-text-700 ring-1 ring-inset lt-ring-200',
            'chip-remove' => 'inline-flex items-center rounded-full p-0.5 lt-text-400 lt-hover-bg-100 lt-hover-text-600 cursor-pointer',
            'clear-all-btn' => 'text-xs font-medium lt-text-500 lt-hover-text-700 cursor-pointer ml-1',
            'pagination-wrapper' => 'px-4 py-3 border-t border-gray-100',
            'per-page-wrapper' => 'flex items-center gap-1.5',
            'per-page-label' => 'text-sm text-gray-500',
            'per-page-select' => 'rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-3 text-sm',
            'empty-state' => 'px-4 py-16 text-center text-sm text-gray-400',
            'sort-icon' => 'inline-block ml-1 text-gray-400',
            'sort-icon-active' => 'inline-block ml-1 lt-text-600',
            'badge-true' => 'inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white',
            'badge-false' => 'inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white',
            'footer' => 'flex flex-col items-center gap-3 sm:flex-row sm:items-center sm:justify-between',
            'results-count' => 'text-sm text-gray-500 text-center sm:text-left',
            'pagination-nav' => 'flex justify-center sm:justify-end',
            'filter-clear-wrapper' => 'pt-3 mt-3 border-t border-gray-100',
            'filter-clear-btn' => 'w-full inline-flex items-center justify-center rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100 transition-colors',
            'bulk-btn' => 'w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-lg bg-gray-100 border border-gray-200 px-3 py-2 text-sm font-medium text-gray-400 cursor-default select-none',
            'bulk-btn-active' => 'w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-lg border lt-border-400 lt-bg-50 px-3 py-2 text-sm font-medium lt-text-700 cursor-pointer transition-colors lt-hover-bg-100',
            'bulk-badge' => 'inline-flex items-center justify-center rounded-full lt-bg-600 px-1.5 text-[10px] font-bold text-white min-w-[18px] leading-[18px]',
            'bulk-dropdown' => 'absolute left-0 sm:left-auto sm:right-0 z-50 mt-2 rounded-lg bg-white shadow-lg ring-1 ring-gray-200 py-1',
            'bulk-dropdown-item' => 'flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors bg-transparent border-none text-left cursor-pointer',
            'bulk-checkbox-th' => 'px-4 py-3 w-10',
            'bulk-checkbox-td' => 'px-4 py-3 w-10',
            'bulk-checkbox' => 'lt-checkbox rounded border-gray-300 h-4 w-4 cursor-pointer',
            'selection-bar' => 'flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-4 py-2 border-b lt-border-100 lt-bg-50-muted',
            'selection-count' => 'text-sm font-semibold lt-text-700',
            'selection-actions' => 'flex flex-wrap items-center gap-2',
            'selection-select-page-btn' => 'inline-flex items-center rounded-lg lt-bg-600 px-3 py-1.5 text-xs font-medium text-white lt-hover-bg-700 transition-colors cursor-pointer border-none',
            'selection-deselect-page-btn' => 'inline-flex items-center rounded-lg bg-white border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer',
            'selection-deselect-btn' => 'inline-flex items-center rounded-lg bg-white border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer',
        ];
    }
}
