<div>
@if(config('livewire-tables.dark_mode.enabled', false))
<div x-data="{ ltDark: document.documentElement.classList.contains('lt-dark') }"
     x-on:lt-dark-toggled.window="ltDark = document.documentElement.classList.contains('lt-dark')"
     :class="ltDark ? 'lt-dark' : ''">
@endif
<div class="{{ $classes['container'] }}">
    @include('livewire-tables::components.styles')
    @once
    <script>
    document.addEventListener('livewire:init', function() {
        if (!window.__ltFilterState) window.__ltFilterState = {};
        // Save ALL open filter panel and dropdown states BEFORE any Livewire request
        Livewire.hook('commit', ({component, commit, respond, succeed, fail}) => {
            document.querySelectorAll('[wire\\:key="lt-filter-toggle"]').forEach(function(el) {
                if (el._x_dataStack && el._x_dataStack[0] && el._x_dataStack[0].open === true) {
                    var tk = el.getAttribute('data-lt-table-key');
                    if (tk) {
                        window.__ltFilterState[tk] = true;
                    }
                }
            });
            document.querySelectorAll('[wire\\:key^="filter-"]').forEach(function(el) {
                if (el._x_dataStack && el._x_dataStack[0] && el._x_dataStack[0].open === true) {
                    var wk = el.getAttribute('wire:key');
                    if (wk) {
                        window.__ltFilterState['lt-ms-' + wk.replace('filter-', '')] = true;
                    }
                }
            });
        });
    });
    </script>
    @endonce
    <div wire:key="lt-chip-section">
        @if(count($activeFilters) > 0 || count($sortChips) > 0)
            @include('livewire-tables::components.filter-chips')
        @endif
    </div>

    <div class="{{ $classes['toolbar'] }}">
        <div class="{{ $classes['toolbar-row'] }}">
            <div class="{{ $classes['toolbar-left'] }} lt-toolbar-mobile">
                {!! $this->resolveSlot($this->toolbarLeftPrepend()) !!}
                <div class="{{ $classes['toolbar-search'] }}">
                    @include('livewire-tables::components.search')
                </div>

                @if(count($filters) > 0)
                    <div wire:key="lt-filter-toggle"
                         data-lt-table-key="{{ $this->getTableKey() }}"
                         class="{{ $classes['toolbar-item'] }}"
                         x-data="{
                            open: false,
                            init() {
                                if (!window.__ltFilterState) window.__ltFilterState = {};
                                let tk = this.$el.getAttribute('data-lt-table-key') || 'unknown';
                                if (window.__ltFilterState[tk] === true) {
                                    this.open = true;
                                }
                                this.$watch('open', (val) => {
                                    window.__ltFilterState[tk] = val;
                                });
                            }
                         }"
                         @click.outside="if ($event.target.closest('.flatpickr-calendar') || document.querySelector('.flatpickr-calendar.open')) return; open = false">
                        <button
                            @click="open = !open"
                            type="button"
                            class="{{ $filterBtnClass }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px">
                                <path fill-rule="evenodd" d="M3.792 2.938A49.069 49.069 0 0112 2.25c2.797 0 5.54.236 8.209.688a1.857 1.857 0 011.541 1.836v1.044a3 3 0 01-.879 2.121l-6.182 6.182a1.5 1.5 0 00-.439 1.061v2.927a3 3 0 01-1.658 2.684l-1.757.878A.75.75 0 019.75 21v-5.818a1.5 1.5 0 00-.44-1.06L3.13 7.938a3 3 0 01-.879-2.121V4.774c0-.897.64-1.683 1.542-1.836z" clip-rule="evenodd" />
                            </svg>
                            <span class="{{ $classes['toolbar-btn-text'] }}">{{ __('livewire-tables::messages.filters') }}</span>
                            @if($this->hasActiveFilters())
                                <span class="{{ $classes['filter-badge'] }}">{{ count($activeFilters) }}</span>
                            @endif
                        </button>
                        <div
                            x-show="open"
                            x-cloak
                            x-transition
                            class="{{ $classes['filter-dropdown'] }} lt-filter-panel"
                            style="min-width:22rem;scrollbar-width:none;-ms-overflow-style:none"
                            @click.stop="$dispatch('lt-dropdown-opened', 'panel')"
                        >
                            @include('livewire-tables::components.filters')
                        </div>
                    </div>
                @endif
                {!! $this->resolveSlot($this->toolbarLeftAppend()) !!}
            </div>

            <div class="{{ $classes['toolbar-right'] }} lt-toolbar-mobile">
                {!! $this->resolveSlot($this->toolbarRightPrepend()) !!}
                @if($this->hasBulkActions())
                <div wire:key="lt-bulk-toggle" class="{{ $classes['toolbar-item'] }}" x-data="{ open: false }" @click.outside="open = false">
                    <button
                        @click="($wire.selectedIds.length > 0 || $wire.selectAllPages) ? open = !open : null"
                        type="button"
                        :class="($wire.selectedIds.length > 0 || $wire.selectAllPages) ? '{{ $bulkBtnActiveClass }}' : '{{ $bulkBtnClass }}'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px">
                            <path fill-rule="evenodd" d="M2.625 6.75a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0A.75.75 0 018.25 6h12a.75.75 0 010 1.5h-12a.75.75 0 01-.75-.75zM2.625 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zM7.5 12a.75.75 0 01.75-.75h12a.75.75 0 010 1.5h-12A.75.75 0 017.5 12zm-4.875 5.25a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0a.75.75 0 01.75-.75h12a.75.75 0 010 1.5h-12a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                        </svg>
                        <span class="{{ $classes['toolbar-btn-text'] }}">{{ __('livewire-tables::messages.bulk_actions') }}</span>
                        <span
                            x-show="$wire.selectedIds.length > 0 || $wire.selectAllPages"
                            x-text="$wire.selectAllPages ? Math.max(0, {{ $totalRows }} - $wire.excludedIds.length) : $wire.selectedIds.length"
                            class="{{ $classes['bulk-badge'] }}"
                        ></span>
                    </button>
                    <div
                        x-show="open"
                        x-cloak
                        x-transition
                        class="{{ $classes['bulk-dropdown'] }}"
                        style="min-width:12rem"
                        @click.stop
                    >
                        @foreach($bulkActions as $bulkKey => $bulkLabel)
                            <button
                                type="button"
                                class="{{ $classes['bulk-dropdown-item'] }}"
                                wire:click="executeBulkAction('{{ $bulkKey }}'); open = false"
                            >{{ $bulkLabel }}</button>
                        @endforeach
                    </div>
                </div>
                @endif
                <div wire:key="lt-column-toggle" class="{{ $classes['toolbar-item'] }}" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" type="button" class="{{ $columnBtnClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px">
                            <path d="M15 3.75H9v16.5h6V3.75zM16.5 3.75v16.5h3.75a.75.75 0 00.75-.75V4.5a.75.75 0 00-.75-.75H16.5zM7.5 3.75H3.75a.75.75 0 00-.75.75v15c0 .414.336.75.75.75H7.5V3.75z" />
                        </svg>
                        <span class="{{ $classes['toolbar-btn-text'] }}">{{ __('livewire-tables::messages.columns') }}</span>
                    </button>
                    <div
                        x-show="open"
                        x-cloak
                        x-transition
                        class="{{ $classes['column-dropdown'] }}"
                        style="width:min(14rem, calc(100vw - 2rem));max-height:70vh;padding:0.75rem;border:1px solid #e5e7eb;z-index:1"
                        @click.stop
                    >
                        @foreach($allColumns as $col)
                            <label class="{{ $classes['column-item'] }}" style="cursor:pointer">
                                <input
                                    type="checkbox"
                                    wire:click="toggleColumn('{{ $col->field() }}')"
                                    @checked($this->isColumnVisible($col->field()))
                                    class="{{ $classes['column-checkbox'] }}"
                                    style="width:1rem;height:1rem;cursor:pointer"
                                >
                                <span class="{{ $classes['column-item-label'] }}">{{ $col->getLabel() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="{{ $classes['toolbar-item'] }} lt-per-page-mobile">
                    @include('livewire-tables::components.per-page')
                </div>
                {!! $this->resolveSlot($this->toolbarRightAppend()) !!}
            </div>
        </div>
    </div>

    @if($this->hasBulkActions())
    <div
        x-show="$wire.selectedIds.length > 0 || $wire.selectAllPages"
        x-cloak
        class="{{ $classes['selection-bar'] }}"
    >
        <span class="{{ $classes['selection-count'] }}"
            x-text="($wire.selectAllPages ? Math.max(0, {{ $totalRows }} - $wire.excludedIds.length) : $wire.selectedIds.length) + ' {{ __('livewire-tables::messages.selected') }}'"
        ></span>
        <div class="{{ $classes['selection-actions'] }} lt-selection-actions-mobile">
            <button type="button" class="{{ $classes['selection-select-page-btn'] }}"
                x-show="$wire.selectAllPages ? $wire.pageIds.some(id => $wire.excludedIds.includes(id)) : !$wire.pageIds.every(id => $wire.selectedIds.includes(id))"
                x-on:click="$wire.setPageSelection($wire.pageIds, true)">
                {{ __('livewire-tables::messages.select_page') }}
            </button>
            <button type="button" class="{{ $classes['selection-deselect-page-btn'] }}"
                x-show="$wire.selectAllPages ? $wire.pageIds.some(id => !$wire.excludedIds.includes(id)) : $wire.pageIds.some(id => $wire.selectedIds.includes(id))"
                x-on:click="$wire.setPageSelection($wire.pageIds, false)">
                {{ __('livewire-tables::messages.deselect_page') }}
            </button>
            <button type="button" class="{{ $classes['selection-deselect-btn'] }}" wire:click="deselectAll">
                {{ __('livewire-tables::messages.deselect_all') }}
            </button>
        </div>
    </div>
    @endif

    {!! $this->resolveSlot($this->beforeTable()) !!}

    <div class="{{ $classes['wrapper'] }}" wire:loading.class="opacity-50" wire:target="search, perPage, sortBy, tableFilters, removeFilter, clearFilters, toggleColumn, executeBulkAction, toggleSelected, setPageSelection, selectAllAcrossPages, deselectAll, gotoPage, previousPage, nextPage">
        <table class="{{ $classes['table'] }}">
            @include('livewire-tables::components.thead')
            @include('livewire-tables::components.tbody')
        </table>
    </div>

    {!! $this->resolveSlot($this->afterTable()) !!}

    <div class="{{ $classes['pagination-wrapper'] }}">
        <div class="{{ $classes['footer'] }}">
            <div class="{{ $classes['results-count'] }}">
                @if($rows->total() > 0)
                    {{ __('livewire-tables::messages.showing') }}
                    <strong>{{ $rows->firstItem() }}</strong>
                    {{ __('livewire-tables::messages.to') }}
                    <strong>{{ $rows->lastItem() }}</strong>
                    {{ __('livewire-tables::messages.of') }}
                    <strong>{{ $rows->total() }}</strong>
                    {{ __('livewire-tables::messages.results') }}
                @else
                    {{ __('livewire-tables::messages.no_results') }}
                @endif
            </div>
            @if($rows->hasPages())
                <div class="{{ $classes['pagination-nav'] }}">
                    {{ $rows->onEachSide(2)->links($paginationView) }}
                </div>
            @endif
        </div>
    </div>
</div>
@if(config('livewire-tables.dark_mode.enabled', false))
</div>
@endif
</div>
