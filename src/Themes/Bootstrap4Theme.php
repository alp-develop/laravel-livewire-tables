<?php

declare(strict_types=1);

namespace Livewire\Tables\Themes;

use Livewire\Tables\Core\Contracts\ThemeContract;

final class Bootstrap4Theme implements ThemeContract
{
    public function name(): string
    {
        return 'bootstrap4';
    }

    public function paginationView(): string
    {
        return 'livewire-tables::components.pagination.bootstrap';
    }

    public function supportsImportantPrefix(): bool
    {
        return false;
    }

    /** @return array<string, string> */
    public function classes(): array
    {
        return [
            'container' => 'card shadow-sm',
            'toolbar' => 'card-header bg-white py-3 px-3',
            'toolbar-row' => 'd-flex flex-column flex-sm-row align-items-stretch align-items-sm-center lt-flex-gap-2',
            'toolbar-left' => 'd-flex flex-column flex-sm-row align-items-stretch align-items-sm-center lt-flex-gap-2 flex-sm-grow-1',
            'toolbar-right' => 'd-flex flex-column flex-sm-row align-items-stretch align-items-sm-center lt-flex-gap-2',
            'toolbar-search' => '',
            'toolbar-item' => 'position-relative',
            'toolbar-btn-text' => 'small',
            'wrapper' => 'table-responsive',
            'table' => 'table table-hover mb-0',
            'thead' => 'thead-light',
            'th' => 'text-uppercase text-secondary font-weight-bold small text-nowrap',
            'th-sortable' => 'cursor-pointer user-select-none',
            'th-sorted' => 'lt-text-700',
            'tbody' => '',
            'tr' => '',
            'td' => 'align-middle',
            'search-wrapper' => 'position-relative',
            'search-input' => 'form-control pl-5',
            'search-icon' => 'position-absolute text-muted',
            'filter-btn' => 'btn lt-btn-neutral d-flex w-100 align-items-center justify-content-center lt-flex-gap-1',
            'filter-btn-active' => 'btn lt-btn-neutral-active d-flex w-100 align-items-center justify-content-center lt-flex-gap-1',
            'filter-badge' => 'badge lt-badge-primary rounded-pill font-weight-bold px-2 ml-1',
            'filter-dropdown' => 'dropdown-menu show position-absolute mt-2 p-3 shadow',
            'filter-wrapper' => 'd-flex flex-column',
            'filter-group' => '',
            'filter-select' => 'custom-select',
            'filter-input' => 'form-control',
            'filter-label' => 'text-uppercase text-secondary small font-weight-bold mb-1',
            'filter-range-row' => 'd-flex align-items-center lt-flex-gap-2',
            'filter-range-separator' => 'text-muted small flex-shrink-0 mx-2',
            'column-btn' => 'btn lt-btn-neutral d-flex w-100 align-items-center justify-content-center lt-flex-gap-1',
            'column-dropdown' => 'position-absolute mt-2 rounded bg-white shadow overflow-auto',
            'column-item' => 'd-flex align-items-center lt-flex-gap-1 py-0 px-1 rounded',
            'column-checkbox' => 'lt-checkbox',
            'column-item-label' => 'small font-weight-normal user-select-none',
            'chip-bar' => 'd-flex flex-wrap align-items-center lt-flex-gap-2 px-3 py-2 border-bottom',
            'chip' => 'badge lt-badge-primary-soft d-inline-flex align-items-center lt-flex-gap-1 font-weight-normal py-1 px-2',
            'chip-remove' => 'btn p-0 border-0 lt-text-400',
            'clear-all-btn' => 'btn btn-link btn-sm lt-text-600 text-decoration-none p-0',
            'pagination-wrapper' => 'card-footer bg-white py-3 px-3',
            'per-page-wrapper' => 'd-flex align-items-center lt-flex-gap-1',
            'per-page-label' => 'text-muted small mb-0',
            'per-page-select' => 'btn border rounded px-3 py-1 bg-white text-dark',
            'empty-state' => 'text-center text-muted py-5',
            'sort-icon' => 'ml-1 text-muted d-inline',
            'sort-icon-active' => 'ml-1 lt-text-600 d-inline',
            'badge-true' => 'd-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white',
            'badge-false' => 'd-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white',
            'footer' => 'd-flex flex-column flex-sm-row align-items-center justify-content-between lt-flex-gap-2',
            'results-count' => 'text-muted small text-center text-sm-left',
            'pagination-nav' => 'd-flex justify-content-center justify-content-sm-end',
            'filter-clear-wrapper' => 'pt-2 mt-2 border-top',
            'filter-clear-btn' => 'd-flex align-items-center justify-content-center w-100 text-danger border-0 rounded px-3 py-2 small font-weight-bold',
            'bulk-btn' => 'btn lt-btn-neutral-disabled d-flex w-100 align-items-center justify-content-center lt-flex-gap-1',
            'bulk-btn-active' => 'btn lt-btn-neutral-active d-flex w-100 align-items-center justify-content-center lt-flex-gap-1',
            'bulk-badge' => 'badge lt-badge-primary rounded-pill font-weight-bold px-2 ml-1',
            'bulk-dropdown' => 'dropdown-menu show position-absolute mt-1 shadow',
            'bulk-dropdown-item' => 'd-flex w-100 align-items-center px-3 py-2 small text-dark text-decoration-none bg-transparent border-0 text-left',
            'bulk-checkbox-th' => 'text-uppercase text-secondary font-weight-bold small',
            'bulk-checkbox-td' => '',
            'bulk-checkbox' => 'lt-checkbox cursor-pointer',
            'selection-bar' => 'd-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between lt-flex-gap-2 px-3 py-2 border-bottom lt-bg-50-muted',
            'selection-count' => 'small font-weight-bold lt-text-700',
            'selection-actions' => 'd-flex flex-wrap align-items-center lt-flex-gap-2',
            'selection-select-page-btn' => 'btn btn-sm lt-btn-primary',
            'selection-deselect-page-btn' => 'btn btn-sm btn-outline-secondary',
            'selection-deselect-btn' => 'btn btn-sm btn-outline-danger',
        ];
    }
}
