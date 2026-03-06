<div
    wire:key="filter-{{ $filter->getKey() }}"
    x-data="{
        options: [
            { value: '1', label: '{{ __('livewire-tables::messages.yes') }}' },
            { value: '0', label: '{{ __('livewire-tables::messages.no') }}' }
        ],
        selected: '',
        open: false,
        hovered: null,
        _uid: '{{ $filter->getKey() }}-' + Math.random().toString(36).substr(2, 9),
        init() {
            let v = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
            this.selected = (v !== null && v !== undefined && v !== '') ? String(v) : '';
        },
        select(value) {
            this.selected = value;
            this.open = false;
            this.$wire.set('tableFilters.{{ $filter->getKey() }}', value);
        },
        clear() {
            this.selected = '';
        },
        labelFor(value) {
            let opt = this.options.find(o => o.value === value);
            return opt ? opt.label : '{{ __('livewire-tables::messages.all') }}';
        },
        optStyle(value) {
            let base = 'cursor:default;transition:background 0.1s;color:var(--lt-text,inherit);';
            if (this.selected === String(value)) return base + 'background:var(--lt-opt-active);color:var(--lt-opt-active-text);font-weight:600';
            if (this.hovered === value) return base + 'background:var(--lt-opt-hover)';
            return base;
        }
    }"
    x-on:remove-filter.window="if ($event.detail && $event.detail.field === '{{ $filter->getKey() }}') { clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', '') }"
    x-on:livewire-tables:clear-filters.window="clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', ''); open = false"
    x-on:lt-dropdown-opened.window="if ($event.detail !== _uid) { open = false }"
    @click.outside="open = false"
    style="position:relative"
>
    <button type="button" x-on:click.stop="$dispatch('lt-dropdown-opened', _uid); open = !open"
        class="{{ $resolvedSelectClass }}"
        style="width:100%;text-align:left;cursor:pointer;padding-right:3rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
    >
        <span x-text="selected === '' ? '{{ __('livewire-tables::messages.all') }}' : labelFor(selected)"></span>
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
        style="position:absolute;left:0;right:0;z-index:9999;margin-top:0.25rem;background:var(--lt-bg-card,#fff);border:1px solid var(--lt-border,#e5e7eb);border-radius:0.375rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);overflow:hidden;color:var(--lt-text,inherit)"
    >
        <div style="max-height:14.3rem;overflow-y:auto">
            <div x-on:click="select('')"
                x-on:mouseenter="hovered = ''"
                x-on:mouseleave="hovered = null"
                :style="optStyle('')"
            >
                <div style="padding:0.375rem 0.75rem;font-size:0.875rem;user-select:none">
                    <span>{{ __('livewire-tables::messages.all') }}</span>
                </div>
            </div>
            <template x-for="opt in options" :key="opt.value">
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
        </div>
    </div>
</div>
