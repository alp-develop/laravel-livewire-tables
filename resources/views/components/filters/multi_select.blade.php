@php
$allOptions = collect(
    $filter->hasDependency()
        ? $filter->resolveOptions($this->resolveParentValue($filter->getParent() ?? ''))
        : $filter->options()
)->map(fn($label, $key) => ['value' => (string) $key, 'label' => (string) $label])->values()->toArray();
@endphp
<div
    wire:key="filter-{{ $filter->getKey() }}"
    x-data="{
        options: {{ Js::from($allOptions) }},
        search: '',
        selected: [],
        committed: [],
        open: false,
        hovered: null,
        _uid: '{{ $filter->getKey() }}-' + Math.random().toString(36).substr(2, 9),
        init() {
            let stored = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
            this.selected = Array.isArray(stored) ? stored.map(String) : [];
            this.committed = [...this.selected];
            if (!window.__ltFilterState) window.__ltFilterState = {};
            let sk = 'lt-ms-{{ $filter->getKey() }}';
            if (window.__ltFilterState[sk] === true) {
                this.open = true;
            }
            this.$watch('open', (val) => {
                window.__ltFilterState[sk] = val;
            });
        },
        toggle(value) {
            let idx = this.selected.indexOf(value);
            if (idx === -1) { this.selected = [...this.selected, value]; }
            else { this.selected = this.selected.filter(v => v !== value); }
            this.commit();
        },
        commit() {
            let a = [...this.selected].sort().join(',');
            let b = [...this.committed].sort().join(',');
            if (a === b) return;
            this.committed = [...this.selected];
            this.$wire.set('tableFilters.{{ $filter->getKey() }}', this.selected);
        },
        clear() {
            this.selected = [];
            this.committed = [];
            this.search = '';
        },
        filteredOptions() {
            if (this.search === '') return this.options;
            let q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        isSelected(value) {
            return this.selected.includes(value);
        },
        labelFor(value) {
            let opt = this.options.find(o => o.value === value);
            return opt ? opt.label : value;
        },
        optStyle(value) {
            let base = 'cursor:pointer;transition:background 0.1s;';
            if (this.isSelected(value)) return base + 'background:var(--lt-primary-200,#d1d5db);color:var(--lt-primary-700,#374151)';
            if (this.hovered === value) return base + 'background:var(--lt-primary-50,#f3f4f6)';
            return base;
        }
    }"
    x-on:remove-filter.window="if ($event.detail && $event.detail.field === '{{ $filter->getKey() }}') { clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', []) }"
    x-on:livewire-tables:clear-filters.window="clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', []); open = false"
    x-on:lt-dropdown-opened.window="if ($event.detail !== _uid) { open = false }"
    @click.outside="open = false"
    style="position:relative"
>
    <div style="position:relative">
        <button
            type="button"
            x-on:click.stop="$dispatch('lt-dropdown-opened', _uid); open = !open"
            class="{{ $resolvedInputClass }}"
            style="width:100%;text-align:left;cursor:pointer;padding-right:3.25rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
        >
            <span x-text="selected.length === 0 ? '{{ addslashes($filter->getPlaceholder() ?: __('livewire-tables::messages.select_option')) }}' : selected.length + ' {{ __('livewire-tables::messages.selected') }}'"></span>
        </button>

        <span
            style="position:absolute;right:0.5rem;top:50%;transform:translateY(-50%);pointer-events:none;display:flex;align-items:center;color:#9ca3af"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
            </svg>
        </span>

        <button
            type="button"
            x-show="selected.length > 0"
            x-cloak
            x-on:click.stop="clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', []); open = false"
            style="position:absolute;right:1.75rem;top:0;bottom:0;display:flex;align-items:center;justify-content:center;padding:0 2px;border:none;background:none;cursor:pointer;color:#6b7280"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='#6b7280'"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;pointer-events:none">
                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
            </svg>
        </button>
    </div>

    <div
        x-show="open"
        x-cloak
        x-transition
        style="position:absolute;left:0;right:0;z-index:9999;margin-top:0.25rem;background:white;border:1px solid #e5e7eb;border-radius:0.375rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);overflow:hidden"
        @click.stop
    >
        @if($filter->isSearchable())
        <div style="padding:0.5rem;border-bottom:1px solid #e5e7eb">
            <input
                type="text"
                x-model="search"
                placeholder="{{ __('livewire-tables::messages.search') }}"
                style="width:100%;padding:0.25rem 0.5rem;border:1px solid #e5e7eb;border-radius:0.25rem;font-size:0.875rem;outline:none"
            />
        </div>
        @endif
        <div style="max-height:14.3rem;overflow-y:auto">
            <template x-for="opt in filteredOptions()" :key="opt.value">
                <div
                    x-on:click="toggle(opt.value)"
                    x-on:mouseenter="hovered = opt.value"
                    x-on:mouseleave="hovered = null"
                    :style="optStyle(opt.value)"
                >
                    <div style="padding:0.375rem 0.75rem;font-size:0.875rem;display:flex;align-items:center;gap:0.5rem;user-select:none">
                        <input
                            type="checkbox"
                            class="lt-checkbox"
                            :value="opt.value"
                            :checked="isSelected(opt.value)"
                            style="pointer-events:none;width:1rem;height:1rem;flex-shrink:0"
                        />
                        <span x-text="opt.label"></span>
                    </div>
                </div>
            </template>
            <template x-if="filteredOptions().length === 0">
                <div style="padding:0.5rem;color:#9ca3af;font-size:0.875rem;text-align:center">{{ __('livewire-tables::messages.no_results') }}</div>
            </template>
        </div>
        <template x-if="selected.length > 0">
            <div style="padding:0.375rem 0.5rem;border-top:1px solid #e5e7eb;flex-shrink:0">
                <button type="button" x-on:click="clear(); $wire.set('tableFilters.{{ $filter->getKey() }}', []); open = false"
                    style="font-size:0.75rem;color:#6b7280;cursor:pointer;background:none;border:none;padding:0"
                >{{ __('livewire-tables::messages.clear_all') }}</button>
            </div>
        </template>
    </div>
</div>
