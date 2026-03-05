<div
    wire:key="filter-{{ $filter->getKey() }}"
    x-data="{
        instance: null,
        selected: [],
        committed: [],
        init() {
            let stored = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
            this.selected = Array.isArray(stored) ? stored.filter(d => d !== '') : [];
            this.committed = [...this.selected];
            this.$nextTick(() => { this.loadFlatpickr(); });
            this.$wire.$watch('tableFilters', () => {
                let sv = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
                let next = Array.isArray(sv) ? sv.filter(d => d !== '') : [];
                if ([...next].sort().join(',') !== [...this.selected].sort().join(',')) {
                    this.selected = next;
                    this.committed = [...next];
                    if (this.instance) {
                        this.instance.setDate(next);
                    }
                }
            });
        },
        loadFlatpickr() {
            if (!document.querySelector('link[href*=\'flatpickr\']')) {
                let link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
                document.head.appendChild(link);
            }
            let boot = () => {
                if (this.instance) this.instance.destroy();
                let config = {
                    mode: 'multiple',
                    dateFormat: '{{ $filter->getFormat() }}',
                    allowInput: false,
                    onChange: (dates) => {
                        let fmt = (d) => flatpickr.formatDate(d, '{{ $filter->getFormat() }}');
                        this.selected = dates.map(fmt);
                        let a = [...this.selected].sort().join(',');
                        let b = [...this.committed].sort().join(',');
                        if (a === b) return;
                        this.committed = [...this.selected];
                        this.$wire.set('tableFilters.{{ $filter->getKey() }}', this.selected);
                    }
                };
                @if($filter->getMinDate())
                config.minDate = '{{ $filter->getMinDate() }}';
                @endif
                @if($filter->getMaxDate())
                config.maxDate = '{{ $filter->getMaxDate() }}';
                @endif
                this.instance = flatpickr(this.$refs.dateInput, config);
                if (this.selected.length > 0) {
                    this.instance.setDate(this.selected);
                }
            };
            if (typeof flatpickr === 'undefined') {
                let script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
                script.onload = boot;
                document.head.appendChild(script);
            } else {
                boot();
            }
        }
    }"
    x-on:remove-filter.window="if ($event.detail && $event.detail.field === '{{ $filter->getKey() }}' && instance) { instance.clear(); selected = []; committed = []; $wire.set('tableFilters.{{ $filter->getKey() }}', []) }"
    x-on:livewire-tables:clear-filters.window="if (instance) { instance.clear(); selected = []; committed = []; } $wire.set('tableFilters.{{ $filter->getKey() }}', [])"
    style="position:relative"
>
    <span style="position:absolute;left:0.625rem;top:50%;transform:translateY(-50%);pointer-events:none;display:flex;align-items:center;color:#9ca3af;z-index:1">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
        </svg>
    </span>
    <input
        x-ref="dateInput"
        type="text"
        class="{{ $resolvedInputClass }}"
        style="width:100%;padding-left:2rem;padding-right:2rem"
        placeholder="{{ $filter->getPlaceholder() ?: __('livewire-tables::messages.select_dates') }}"
        readonly
    />
    <button
        type="button"
        x-show="selected.length > 0"
        x-cloak
        x-on:click.stop="if (instance) { instance.clear(); } selected = []; committed = []; $wire.set('tableFilters.{{ $filter->getKey() }}', [])"
        style="position:absolute;right:0.5rem;top:0;bottom:0;display:flex;align-items:center;justify-content:center;padding:0 2px;border:none;background:none;cursor:pointer;color:#6b7280;z-index:2"
        onmouseover="this.style.color='#dc2626'"
        onmouseout="this.style.color='#6b7280'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;pointer-events:none">
            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
        </svg>
    </button>
</div>
