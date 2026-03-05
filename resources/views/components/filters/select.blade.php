@php
$selectOptions = collect(
    $filter->hasDependency()
        ? $filter->resolveOptions($this->resolveParentValue($filter->getParent() ?? ''))
        : $filter->options()
)
    ->reject(fn($label, $key) => (string) $key === '')
    ->map(fn($label, $key) => ['value' => (string) $key, 'label' => (string) $label])
    ->values()
    ->toArray();
$placeholderLabel = addslashes($filter->getPlaceholder() ?: __('livewire-tables::messages.select_option'));
$isDisabled = $filter->hasDependency() && $this->resolveParentValue($filter->getParent() ?? '') === '';
$wireKey = $filter->hasDependency()
    ? 'filter-' . $filter->getKey() . '-' . ($this->resolveParentValue($filter->getParent() ?? '') ?: 'none')
    : 'filter-' . $filter->getKey();
@endphp
<div
    wire:key="{{ $wireKey }}"
    x-data="{
        options: {{ Js::from($selectOptions) }},
        search: '',
        selected: '',
        open: false,
        hovered: null,
        _uid: '{{ $filter->getKey() }}-' + Math.random().toString(36).substr(2, 9),
        init() {
            this.selected = String(this.$wire.get('tableFilters.{{ $filter->getKey() }}') ?? '');
        },
        select(value) {

            this.selected = value;
            this.search = '';
            this.open = false;
            this.$wire.set('tableFilters.{{ $filter->getKey() }}', value);
        },
        clear() {
            this.selected = '';
            this.search = '';
        },
        filteredOptions() {
            if (this.search === '') return this.options;
            let q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        labelFor(value) {
            let opt = this.options.find(o => o.value === value);
            return opt ? opt.label : '';
        },
        optStyle(value) {
            let base = 'cursor:default;transition:background 0.1s;';
            if (this.selected === String(value)) return base + 'background:var(--lt-primary-200,#d1d5db);color:var(--lt-primary-700,#374151);font-weight:600';
            if (this.hovered === value) return base + 'background:var(--lt-primary-50,#f3f4f6)';
            return base;
        }
    }"
    x-on:remove-filter.window="if ($event.detail && $event.detail.field === '{{ $filter->getKey() }}') { clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', '') }"
    x-on:livewire-tables:clear-filters.window="clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', ''); open = false"
    x-on:lt-dropdown-opened.window="if ($event.detail !== _uid) { open = false; search = '' }"
    @click.outside="open = false; search = ''"
    style="position:relative"
>
    <button type="button"
        x-on:click.stop="{{ $isDisabled ? '' : '$dispatch(\'lt-dropdown-opened\', _uid); open = !open' }}"
        class="{{ $resolvedSelectClass }}"
        style="width:100%;text-align:left;cursor:{{ $isDisabled ? 'default' : 'pointer' }};padding-right:3rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis{{ $isDisabled ? ';opacity:0.5' : '' }}"
    >
        <span x-text="selected === '' ? '{{ $placeholderLabel }}' : labelFor(selected)"></span>
    </button>
    <button type="button" x-show="selected !== ''" x-cloak x-on:click.stop="select('')"
        style="position:absolute;right:2.25rem;top:0;bottom:0;display:flex;align-items:center;justify-content:center;padding:0 2px;border:none;background:none;cursor:pointer;color:#6b7280;z-index:1"
        onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#6b7280'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;pointer-events:none">
            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
        </svg>
    </button>
    <div x-show="open" x-cloak x-transition @click.stop
        style="position:absolute;left:0;right:0;z-index:9999;margin-top:0.25rem;background:white;border:1px solid #e5e7eb;border-radius:0.375rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);overflow:hidden"
    >
        @if($filter->isSearchable())
        <div style="padding:0.5rem;border-bottom:1px solid #e5e7eb">
            <input type="text" x-model="search" x-ref="searchInput"
                placeholder="{{ __('livewire-tables::messages.search') }}"
                style="width:100%;padding:0.25rem 0.5rem;border:1px solid #e5e7eb;border-radius:0.25rem;font-size:0.875rem;outline:none"
            />
        </div>
        @endif
        <div style="max-height:14.3rem;overflow-y:auto">
            <template x-for="opt in filteredOptions()" :key="opt.value">
                <div x-on:click="select(opt.value)"
                    x-on:mouseenter="hovered = opt.value"
                    x-on:mouseleave="hovered = null"
                    :style="optStyle(opt.value)"
                >
                    <div style="padding:0.375rem 0.75rem;font-size:0.875rem;user-select:none">
                        <span x-text="opt.label"></span>
                    </div>
                </div>
            </template>
            <template x-if="filteredOptions().length === 0">
                <div style="padding:0.5rem;color:#9ca3af;font-size:0.875rem;text-align:center">{{ __('livewire-tables::messages.no_results') }}</div>
            </template>
        </div>
    </div>
</div>
