@php $sc = static fn(string $v): string => preg_match('/^[a-zA-Z0-9#(). ,%\-\/]+$/', $v) ? $v : ''; @endphp
<style>
    :root {
        --lt-primary-50: {{ $sc(config('livewire-tables.colors.50', '#f0fdfa')) }};
        --lt-primary-100: {{ $sc(config('livewire-tables.colors.100', '#ccfbf1')) }};
        --lt-primary-200: {{ $sc(config('livewire-tables.colors.200', '#99f6e4')) }};
        --lt-primary-400: {{ $sc(config('livewire-tables.colors.400', '#2dd4bf')) }};
        --lt-primary-500: {{ $sc(config('livewire-tables.colors.500', '#14b8a6')) }};
        --lt-primary-600: {{ $sc(config('livewire-tables.colors.600', '#0d9488')) }};
        --lt-primary-700: {{ $sc(config('livewire-tables.colors.700', '#0f766e')) }};
        /* Chrome — light mode base */
        --lt-bg: #ffffff;
        --lt-bg-card: #ffffff;
        --lt-bg-subtle: #f9fafb;
        --lt-border: #e5e7eb;
        --lt-border-light: #f3f4f6;
        --lt-text: #111827;
        --lt-text-muted: #6b7280;
        /* Option states (select/boolean dropdowns) */
        --lt-opt-hover: var(--lt-primary-50);
        --lt-opt-active: var(--lt-primary-200);
        --lt-opt-active-text: var(--lt-primary-700);
    }
    /* Background */
    .lt-bg-50{background-color:var(--lt-primary-50)!important}
    .lt-bg-600{background-color:var(--lt-primary-600)!important}
    .lt-bg-700{background-color:var(--lt-primary-700)!important}
    .lt-bg-50-muted{background-color:color-mix(in srgb,var(--lt-primary-50) 70%,transparent)!important}
    /* Text */
    .lt-text-400{color:var(--lt-primary-400)!important}
    .lt-text-500{color:var(--lt-primary-500)!important}
    .lt-text-600{color:var(--lt-primary-600)!important}
    .lt-text-700{color:var(--lt-primary-700)!important}
    /* Border */
    .lt-border-100{border-color:var(--lt-primary-100)!important}
    .lt-border-400{border-color:var(--lt-primary-400)!important}
    .lt-border-600{border-color:var(--lt-primary-600)!important}
    .lt-ring-200{--tw-ring-color:var(--lt-primary-200)}
    /* Hover */
    .lt-hover-bg-100:hover{background-color:var(--lt-primary-100)!important}
    .lt-hover-bg-700:hover{background-color:var(--lt-primary-700)!important}
    .lt-hover-text-600:hover{color:var(--lt-primary-600)!important}
    .lt-hover-text-700:hover{color:var(--lt-primary-700)!important}
    .lt-hover-theme:hover{background-color:var(--lt-primary-50)!important;color:var(--lt-primary-700)!important;border-color:var(--lt-primary-400)!important}
    /* Checkbox */
    .lt-checkbox{accent-color:var(--lt-primary-600);color:var(--lt-primary-600)}
    .lt-checkbox:checked{background-color:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important}
    .lt-checkbox:focus{outline:2px solid var(--lt-primary-500);outline-offset:2px;box-shadow:0 0 0 3px var(--lt-primary-100)!important}
    /* Column dropdown item hover */
    label:has(.lt-checkbox):hover{background-color:var(--lt-primary-50)!important;cursor:pointer}
    /* Bulk dropdown item hover */
    .lt-bulk-dropdown-item:hover{background-color:var(--lt-opt-hover)!important;color:var(--lt-opt-active-text)!important;cursor:pointer}
    label:has(.lt-checkbox):hover .lt-checkbox{border-color:var(--lt-primary-400)!important}
    label:has(.lt-checkbox):hover .lt-checkbox:checked{border-color:var(--lt-primary-600)!important}
    /* Bulk action checkboxes */
    .lt-bulk-checkbox{accent-color:var(--lt-primary-600);color:var(--lt-primary-600)}
    .lt-bulk-checkbox:checked{background-color:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important}
    .lt-bulk-checkbox:focus{outline:2px solid var(--lt-primary-500);outline-offset:2px;box-shadow:0 0 0 3px var(--lt-primary-100)!important}
    .lt-focus-ring-500:focus{box-shadow:0 0 0 2px var(--lt-primary-500)}
    /* Buttons */
    .lt-btn-primary{background-color:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important;color:#fff!important}
    .lt-btn-primary:hover{background-color:var(--lt-primary-700)!important;border-color:var(--lt-primary-700)!important}
    .lt-btn-outline-primary{color:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important;background-color:transparent!important}
    .lt-btn-outline-primary:hover{background-color:var(--lt-primary-600)!important;color:#fff!important}
    /* Subtle toolbar buttons — uses configured lt-primary theme color */
    .lt-btn-subtle{color:var(--lt-primary-600)!important;border:1px solid var(--lt-primary-400)!important;background-color:transparent!important}
    .lt-btn-subtle:hover{background-color:var(--lt-primary-100)!important;border-color:var(--lt-primary-500)!important;color:var(--lt-primary-700)!important}
    .lt-btn-active-soft{background-color:var(--lt-primary-100)!important;border-color:var(--lt-primary-500)!important;color:var(--lt-primary-700)!important}
    .lt-btn-active-soft:hover{background-color:var(--lt-primary-200)!important;border-color:var(--lt-primary-500)!important}
    /* Neutral toolbar buttons — gray text/border matching Tailwind defaults */
    .lt-btn-neutral{color:var(--lt-text)!important;border:1px solid var(--lt-border)!important;background-color:var(--lt-bg-card)!important}
    .lt-btn-neutral:hover{background-color:var(--lt-primary-50)!important;color:var(--lt-primary-700)!important;border-color:var(--lt-primary-400)!important}
    .lt-btn-neutral-active{background-color:var(--lt-primary-50)!important;border-color:var(--lt-primary-400)!important;color:var(--lt-primary-700)!important}
    .lt-btn-neutral-active:hover{background-color:var(--lt-primary-100)!important;border-color:var(--lt-primary-500)!important}
    .lt-btn-neutral-disabled{color:var(--lt-text-muted)!important;border:1px solid var(--lt-border)!important;background-color:var(--lt-bg-subtle)!important;cursor:default!important}
    /* Thead tinted — specificity (0,1,1) beats text-secondary on th */
    .lt-thead-tinted{background-color:var(--lt-primary-100)!important}
    .lt-thead-tinted th{color:var(--lt-primary-700)!important;background-color:var(--lt-primary-100)!important}
    /* Badges */
    .lt-badge-primary{background-color:var(--lt-primary-600)!important;color:#fff!important}
    .lt-badge-primary-soft{background-color:var(--lt-primary-50)!important;color:var(--lt-primary-700)!important;border:1px solid var(--lt-primary-200)!important}
    /* Form inputs focus */
    .form-select:focus,.form-control:focus,.custom-select:focus{border-color:var(--lt-primary-500)!important;box-shadow:0 0 0 3px var(--lt-primary-100)!important;outline:0!important;background-color:var(--lt-bg-card)!important}
    .lt-select:focus,.lt-input:focus{border-color:var(--lt-primary-500)!important;--tw-ring-color:var(--lt-primary-500);outline:none!important;box-shadow:0 0 0 3px var(--lt-primary-100)!important}
    /* Native select option highlight (best-effort, browser-dependent) */
    .form-select option:checked,.lt-select option:checked,.custom-select option:checked{background-color:var(--lt-primary-200);color:var(--lt-primary-700)}
    /* Flatpickr calendar — use theme primary color */
    .flatpickr-day.selected,.flatpickr-day.startRange,.flatpickr-day.endRange,.flatpickr-day.selected.inRange,.flatpickr-day.startRange.inRange,.flatpickr-day.endRange.inRange{background:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important;color:#fff!important}
    .flatpickr-day.inRange{background:var(--lt-primary-50)!important;border-color:var(--lt-primary-100)!important;box-shadow:-5px 0 0 var(--lt-primary-50),5px 0 0 var(--lt-primary-50)!important}
    .flatpickr-day:hover{background:var(--lt-primary-100)!important;border-color:var(--lt-primary-200)!important}
    .flatpickr-day.today{border-color:var(--lt-primary-400)!important}
    .flatpickr-day.today:hover{background:var(--lt-primary-100)!important;border-color:var(--lt-primary-400)!important}
    /* Pagination */
    .pagination .page-link{color:var(--lt-text);border-color:var(--lt-border);background-color:var(--lt-bg-card);font-weight:600}
    .pagination .page-link:hover{color:var(--lt-text);background-color:var(--lt-bg-subtle);border-color:var(--lt-border);z-index:1}
    .pagination .page-item.disabled .page-link{color:var(--lt-text-muted);background-color:var(--lt-bg-card);border-color:var(--lt-border)}
    [aria-current="page"]>span{background-color:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important;color:#fff!important}
    .page-item.active .page-link{background-color:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important;color:#fff!important}
    .page-link:focus{box-shadow:0 0 0 3px var(--lt-primary-100)!important}
    /* Alpine x-show compatibility with Bootstrap !important display utilities */
    [style*="display: none"]{display:none!important}
    /* Bootstrap table cell padding — match Tailwind px-4 (1rem) / py-3 (0.75rem) */
    .table.mb-0{--bs-table-cell-padding-y:.75rem;--bs-table-cell-padding-x:1rem}
    .table.mb-0 td,.table.mb-0 th{padding:.75rem 1rem}
    /* Misc */
    .lt-filter-panel::-webkit-scrollbar{display:none}
    /* On mobile, filter panel needs overflow visible so select dropdowns can extend beyond */
    @media (max-width: 639px) {
        .lt-filter-panel{overflow:visible!important}
    }
    /* Normalize filter panel sizing — match Tailwind text-sm / text-xs */
    .lt-filter-panel .form-control,.lt-filter-panel .form-select,.lt-filter-panel .custom-select{font-size:.875rem}
    .lt-filter-panel .form-label{font-size:.75rem;letter-spacing:.05em;margin-bottom:.25rem}
    .lt-filter-panel .d-flex.flex-column{gap:.75rem}
    .lt-select{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");background-position:right .75rem center;background-repeat:no-repeat;background-size:1rem}
    /* Flex gap polyfill for Bootstrap 4 (which lacks gap-* utilities) */
    .lt-flex-gap-1{gap:.25rem}
    .lt-flex-gap-2{gap:.5rem}
    /* Per-page button (Bootstrap): inherit dark mode colors */
    .lt-per-page-btn{background-color:var(--lt-bg-card,#fff)!important;color:var(--lt-text,#212529)!important;border-color:var(--lt-border,#dee2e6)!important}
    /* Soft background utilities (cross-framework) */
    .lt-bg-success-soft{background-color:rgba(40,167,69,.1)!important}
    /* ═══════════════ MOBILE / RESPONSIVE ═══════════════ */
    @media (max-width: 639px) {
        /* All toolbar items stretch full width on mobile */
        .lt-toolbar-mobile > div,
        .lt-toolbar-mobile > div > [wire\:key]{width:100%!important}
        /* Toolbar buttons: force full width including custom-styled ones (only direct toolbar buttons) */
        .lt-toolbar-mobile > [wire\:key] > button,
        .lt-toolbar-mobile > div > [wire\:key] > button{width:100%!important;justify-content:center!important}
        /* Filter panel dropdown: absolute, full-width of parent on mobile */
        .lt-filter-panel{left:0!important;right:0!important;min-width:0!important;width:100%!important;max-width:100%!important;position:absolute!important;z-index:50!important}
        /* Column dropdown: full width of parent on mobile */
        [wire\:key="lt-column-toggle"] > div[x-show]{left:0!important;right:0!important;width:100%!important;position:absolute!important;z-index:50!important}
        /* Bulk dropdown: full width of parent on mobile */
        [wire\:key="lt-bulk-toggle"] > div[x-show]{left:0!important;right:0!important;width:100%!important;position:absolute!important;z-index:50!important;min-width:0!important}
        /* Per-page: button and dropdown full width */
        .lt-per-page-mobile{width:100%!important}
        .lt-per-page-mobile > div{width:100%!important}
        .lt-per-page-mobile button{width:100%!important;min-width:0!important;justify-content:space-between!important}
        .lt-per-page-mobile div[x-show]{left:0!important;right:0!important;width:100%!important;min-width:0!important}
        /* Selection bar: stack buttons vertically */
        .lt-selection-actions-mobile{width:100%!important;flex-direction:column!important}
        .lt-selection-actions-mobile button,
        .lt-selection-actions-mobile a{width:100%!important;justify-content:center!important;text-align:center!important}
        /* Pagination: hide far-away page numbers on mobile */
        .lt-page-hide-mobile{display:none!important}
    }
    @media (min-width: 640px) {
        .w-sm-auto{width:auto!important}
        /* Reset per-page to natural size on desktop */
        .lt-per-page-mobile button{min-width:4.5rem!important}
    }

    @if(config('livewire-tables.dark_mode.enabled', false))
    /* ═══════════════ DARK MODE ═══════════════ */
    @php
        $dk = '.lt-dark';
        $dc = config('livewire-tables.dark_mode.colors', []);
        $dBg       = $sc($dc['bg']         ?? '#0f172a');
        $dCard     = $sc($dc['bg-card']    ?? '#1e293b');
        $dSubtle   = $sc($dc['bg-subtle']  ?? '#334155');
        $dBorder   = $sc($dc['border']     ?? '#334155');
        $dText     = $sc($dc['text']       ?? '#f1f5f9');
        $dMuted    = $sc($dc['text-muted'] ?? '#94a3b8');
    @endphp

    /* ── Variable overrides ── */
    {{ $dk }} {
        --lt-bg: {{ $dBg }};
        --lt-bg-card: {{ $dCard }};
        --lt-bg-subtle: {{ $dSubtle }};
        --lt-border: {{ $dBorder }};
        --lt-border-light: {{ $dCard }};
        --lt-text: {{ $dText }};
        --lt-text-muted: {{ $dMuted }};
        /* Option states — dark mode */
        --lt-opt-hover: rgba(255,255,255,.06);
        --lt-opt-active: rgba(255,255,255,.12);
        --lt-opt-active-text: var(--lt-primary-400);
        /* Bootstrap 5.3 native variable overrides */
        --bs-body-bg: {{ $dBg }};
        --bs-body-color: {{ $dText }};
        --bs-emphasis-color: {{ $dText }};
        --bs-secondary-color: {{ $dMuted }};
        --bs-border-color: {{ $dBorder }};
        --bs-card-bg: {{ $dCard }};
        --bs-card-border-color: {{ $dBorder }};
        --bs-card-cap-bg: {{ $dCard }};
        --bs-table-bg: transparent;
        --bs-table-color: {{ $dText }};
        --bs-table-border-color: {{ $dBorder }};
        --bs-table-hover-bg: rgba(255,255,255,.04);
        --bs-table-hover-color: {{ $dText }};
        --bs-table-striped-bg: rgba(255,255,255,.03);
        --bs-table-striped-color: {{ $dText }};
        --bs-table-active-bg: rgba(255,255,255,.06);
        --bs-table-active-color: {{ $dText }};
        --bs-light-bg-subtle: {{ $dSubtle }};
        --bs-heading-color: {{ $dText }};
        --bs-link-color: #818cf8;
        --bs-link-hover-color: #a5b4fc;
        --bs-code-color: #e879f9;
    }

    /* ── Bootstrap class overrides ── */
    {{ $dk }} .table{background:var(--lt-bg-card)!important;color:var(--lt-text)!important}
    {{ $dk }} .table thead th{background:var(--lt-bg-subtle)!important;color:var(--lt-text-muted)!important;border-color:var(--lt-border)!important}
    {{ $dk }} .table td,{{ $dk }} .table th{border-color:var(--lt-border)!important;color:var(--lt-text)!important}
    {{ $dk }} .table>:not(caption)>*>*{border-bottom-color:var(--lt-border)!important}
    {{ $dk }} .table-bordered,{{ $dk }} .table-bordered td,{{ $dk }} .table-bordered th{border-color:var(--lt-border)!important}
    {{ $dk }} .table-striped tbody tr:nth-of-type(odd){background:rgba(255,255,255,.02)!important}
    {{ $dk }} .table tbody tr:hover,{{ $dk }} .table-hover tbody tr:hover{background:rgba(255,255,255,.04)!important}
    {{ $dk }} .table-hover>tbody>tr:hover>*{--bs-table-accent-bg:rgba(255,255,255,.04)!important;color:var(--lt-text)!important}
    {{ $dk }} .table-danger{background:rgba(220,53,69,.12)!important;color:#fca5a5!important}

    {{ $dk }} input,{{ $dk }} select,{{ $dk }} textarea,
    {{ $dk }} .form-control,{{ $dk }} .form-select,{{ $dk }} .custom-select{
        background-color:var(--lt-bg-card)!important;color:var(--lt-text)!important;border-color:var(--lt-border)!important
    }
    {{ $dk }} .form-control:focus,{{ $dk }} .form-select:focus,{{ $dk }} .custom-select:focus{
        background-color:var(--lt-bg-card)!important;border-color:var(--lt-primary-500)!important;box-shadow:0 0 0 .2rem rgba(99,102,241,.25)!important
    }
    {{ $dk }} .form-check-input{background-color:var(--lt-bg-card)!important;border-color:var(--lt-border)!important}
    {{ $dk }} .form-check-input:checked{background-color:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important}
    {{ $dk }} .card{background-color:var(--lt-bg-card)!important;border-color:var(--lt-border)!important;color:var(--lt-text)!important}
    {{ $dk }} .card-header,{{ $dk }} .card-footer{background-color:var(--lt-bg-card)!important;border-color:var(--lt-border)!important;color:var(--lt-text)!important}
    {{ $dk }} .dropdown-menu{background-color:var(--lt-bg-card)!important;border-color:var(--lt-border)!important;color:var(--lt-text)!important}
    {{ $dk }} .dropdown-item{color:var(--lt-text)!important}
    {{ $dk }} .dropdown-item:hover,{{ $dk }} .dropdown-item:focus{background:rgba(255,255,255,.06)!important}
    {{ $dk }} .bg-white{background-color:var(--lt-bg-card)!important}
    {{ $dk }} .bg-light{background-color:var(--lt-bg-subtle)!important}
    {{ $dk }} .bg-danger.bg-opacity-10{background-color:rgba(220,53,69,.12)!important}
    {{ $dk }} .bg-success.bg-opacity-10{background-color:rgba(25,135,84,.12)!important}
    {{ $dk }} .shadow,{{ $dk }} .shadow-sm{box-shadow:0 1px 3px rgba(0,0,0,.3)!important}
    {{ $dk }} .text-secondary,{{ $dk }} .text-muted{color:var(--lt-text-muted)!important}
    {{ $dk }} .text-dark{color:var(--lt-text)!important}
    {{ $dk }} .text-success{color:#34d399!important}
    {{ $dk }} .text-danger{color:#f87171!important}
    {{ $dk }} .border,{{ $dk }} .border-top,{{ $dk }} .border-bottom{border-color:var(--lt-border)!important}
    {{ $dk }} .btn-outline-secondary{color:var(--lt-text-muted)!important;border-color:var(--lt-border)!important}
    {{ $dk }} .btn-outline-secondary:hover{background:rgba(255,255,255,.06)!important;color:var(--lt-text)!important}
    {{ $dk }} .page-link{background:var(--lt-bg-card)!important;color:var(--lt-text)!important;border-color:var(--lt-border)!important}
    {{ $dk }} .page-link:hover{background:var(--lt-bg-subtle)!important}
    {{ $dk }} .page-item.active .page-link{background:var(--lt-primary-600)!important;border-color:var(--lt-primary-600)!important;color:#fff!important}
    {{ $dk }} .page-item.disabled .page-link{background:var(--lt-bg-card)!important;color:var(--lt-text-muted)!important;border-color:var(--lt-border)!important}
    {{ $dk }} .modal-content{background:var(--lt-bg-card)!important;border-color:var(--lt-border)!important;color:var(--lt-text)!important}
    {{ $dk }} .close,{{ $dk }} .btn-close{color:var(--lt-text)!important;filter:invert(1)}
    {{ $dk }} .nav-tabs{border-color:var(--lt-border)!important}
    {{ $dk }} .nav-tabs .nav-link{color:var(--lt-text-muted)!important;border-color:transparent!important}
    {{ $dk }} .nav-tabs .nav-link:hover{border-color:var(--lt-border)!important}
    {{ $dk }} .nav-tabs .nav-link.active{color:var(--lt-text)!important;background:var(--lt-bg-card)!important;border-color:var(--lt-border) var(--lt-border) var(--lt-bg)!important}
    {{ $dk }} code{background:var(--lt-bg-subtle)!important;color:#e879f9!important}
    {{ $dk }} .btn{color:var(--lt-text)}
    {{ $dk }} .rounded,{{ $dk }} .rounded-lg,{{ $dk }} .rounded-xl{border-color:var(--lt-border)}

    /* ── Tailwind utility class overrides ── */
    {{ $dk }} .bg-gray-50,{{ $dk }} .bg-gray-100{background-color:var(--lt-bg-subtle)!important}
    {{ $dk }} .border-gray-200,{{ $dk }} .border-gray-100,{{ $dk }} .border-gray-300{border-color:var(--lt-border)!important}
    {{ $dk }} .divide-gray-200>:not([hidden])~:not([hidden]),{{ $dk }} .divide-gray-100>:not([hidden])~:not([hidden]){border-color:var(--lt-border)!important}
    {{ $dk }} .text-gray-700{color:#e2e8f0!important}
    {{ $dk }} .text-gray-600{color:#cbd5e1!important}
    {{ $dk }} .text-gray-500{color:var(--lt-text-muted)!important}
    {{ $dk }} .text-gray-400{color:#64748b!important}
    {{ $dk }} .text-gray-900{color:var(--lt-text)!important}
    {{ $dk }} .ring-gray-200{--tw-ring-color:{{ $dBorder }}!important}
    {{ $dk }} .shadow-lg{box-shadow:0 4px 12px rgba(0,0,0,.4)!important}
    {{ $dk }} .hover\:bg-gray-50:hover{background:rgba(255,255,255,.05)!important}
    {{ $dk }} .hover\:text-gray-900:hover,{{ $dk }} .hover\:text-gray-700:hover{color:var(--lt-text)!important}
    {{ $dk }} .placeholder-gray-400::placeholder{color:#64748b!important}
    {{ $dk }} .lt-select,{{ $dk }} .lt-input{background-color:var(--lt-bg-card)!important;color:var(--lt-text)!important;border-color:var(--lt-border)!important}
    {{ $dk }} .lt-select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2394a3b8'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E")!important}
    {{ $dk }} .form-select{background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e")!important}
    {{ $dk }} .custom-select{background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3e%3cpath fill='%2394a3b8' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e")!important}
    {{ $dk }} .bg-red-50{background:rgba(239,68,68,.12)!important}
    {{ $dk }} .text-red-900{color:#fca5a5!important}
    {{ $dk }} .bg-green-50{background:rgba(16,185,129,.1)!important}
    {{ $dk }} .text-green-700{color:#6ee7b7!important}
    {{ $dk }} .bg-indigo-50{background:rgba(99,102,241,.12)!important}
    {{ $dk }} .text-indigo-700{color:#a5b4fc!important}
    {{ $dk }} .bg-violet-50{background:rgba(139,92,246,.1)!important}
    {{ $dk }} .text-violet-700{color:#c4b5fd!important}
    {{ $dk }} .bg-sky-50{background:rgba(14,165,233,.1)!important}
    {{ $dk }} .text-sky-700{color:#7dd3fc!important}
    {{ $dk }} .border-b{border-color:var(--lt-border)!important}
    {{ $dk }} .border-transparent{border-color:transparent!important}

    /* ── Chip / badge dark overrides ── */
    {{ $dk }} .lt-bg-50{background-color:rgba(255,255,255,.08)!important}
    {{ $dk }} .lt-bg-50-muted{background-color:rgba(255,255,255,.05)!important}
    {{ $dk }} .lt-text-700{color:var(--lt-primary-400)!important}
    {{ $dk }} .lt-text-400{color:var(--lt-primary-400)!important}
    {{ $dk }} .lt-ring-200{--tw-ring-color:var(--lt-border)}
    {{ $dk }} .lt-badge-primary-soft{background-color:rgba(255,255,255,.08)!important;color:var(--lt-primary-400)!important;border-color:var(--lt-border)!important}
    {{ $dk }} .lt-hover-bg-100:hover{background-color:rgba(255,255,255,.12)!important}
    {{ $dk }} .lt-hover-text-600:hover{color:var(--lt-primary-400)!important}
    {{ $dk }} .lt-text-500{color:var(--lt-primary-400)!important}
    {{ $dk }} .lt-text-600{color:var(--lt-primary-400)!important}
    {{ $dk }} .lt-hover-text-700:hover{color:var(--lt-primary-200)!important}

    /* ── Flatpickr dark mode ── */
    {{ $dk }} .flatpickr-calendar{background:var(--lt-bg-card)!important;border-color:var(--lt-border)!important;box-shadow:0 4px 12px rgba(0,0,0,.4)!important}
    {{ $dk }} .flatpickr-months,.flatpickr-weekdays{background:var(--lt-bg-card)!important}
    {{ $dk }} span.flatpickr-weekday{color:var(--lt-text-muted)!important}
    {{ $dk }} .flatpickr-day{color:var(--lt-text)!important}
    {{ $dk }} .flatpickr-day.prevMonthDay,{{ $dk }} .flatpickr-day.nextMonthDay{color:var(--lt-text-muted)!important}
    {{ $dk }} .flatpickr-current-month .flatpickr-monthDropdown-months,{{ $dk }} .flatpickr-current-month input.cur-year{color:var(--lt-text)!important}
    @endif
    [x-cloak]{display:none!important}
</style>
