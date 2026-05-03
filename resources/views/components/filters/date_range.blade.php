@once
<link rel="preload" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" as="style" crossorigin="anonymous">
<link rel="preload" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js" as="script" crossorigin="anonymous">
@endonce
<div
    wire:key="filter-{{ $filter->getKey() }}"
    x-data="{
        instance: null,
        hasValue: false,
        init() {
            this.$nextTick(() => {
                this.loadFlatpickr();
            });
        },
        loadFlatpickr() {
            if (!document.querySelector('link[href*=\'flatpickr\']')) {
                let link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css';
                link.integrity = 'sha384-RkASv+6KfBMW9eknReJIJ6b3UnjKOKC5bOUaNgIY778NFbQ8MtWq9Lr/khUgqtTt';
                link.crossOrigin = 'anonymous';
                document.head.appendChild(link);
            }
            let boot = () => {
                if (this.instance) this.instance.destroy();
                let config = {
                    mode: 'range',
                    dateFormat: '{{ $filter->getFormat() }}',
                    allowInput: true,
                    clickOpens: true,
                    disableMobile: true,
                    onChange: (dates) => {
                        let fmt = (d) => flatpickr.formatDate(d, '{{ $filter->getFormat() }}');
                        if (dates.length === 2) {
                            this.hasValue = true;
                            $wire.set('tableFilters.{{ $filter->getKey() }}.from', fmt(dates[0]));
                            $wire.set('tableFilters.{{ $filter->getKey() }}.to', fmt(dates[1]));
                        } else if (dates.length === 1) {
                            this.hasValue = true;
                            $wire.set('tableFilters.{{ $filter->getKey() }}.from', fmt(dates[0]));
                            $wire.set('tableFilters.{{ $filter->getKey() }}.to', fmt(dates[0]));
                        }
                    },
                    onClose: (dates) => {
                        let fmt = (d) => flatpickr.formatDate(d, '{{ $filter->getFormat() }}');
                        if (dates.length === 2) {
                            this.hasValue = true;
                            $wire.set('tableFilters.{{ $filter->getKey() }}.from', fmt(dates[0]));
                            $wire.set('tableFilters.{{ $filter->getKey() }}.to', fmt(dates[1]));
                        } else if (dates.length === 1) {
                            this.hasValue = true;
                            $wire.set('tableFilters.{{ $filter->getKey() }}.from', fmt(dates[0]));
                            $wire.set('tableFilters.{{ $filter->getKey() }}.to', fmt(dates[0]));
                        } else {
                            this.hasValue = false;
                            $wire.set('tableFilters.{{ $filter->getKey() }}.from', '');
                            $wire.set('tableFilters.{{ $filter->getKey() }}.to', '');
                        }
                    }
                };
                @if($filter->getMinDate())
                config.minDate = '{{ $filter->getMinDate() }}';
                @endif
                @if($filter->getMaxDate())
                config.maxDate = '{{ $filter->getMaxDate() }}';
                @endif
                @if($filter->getCalendarClass())
                config.className = '{{ $filter->getCalendarClass() }}';
                @endif
                this.instance = flatpickr(this.$refs.dateInput, config);
                let from = $wire.get('tableFilters.{{ $filter->getKey() }}.from');
                if (from) this.hasValue = true;
            };
            if (typeof flatpickr === 'undefined') {
                let script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js';
                script.integrity = 'sha384-5JqMv4L/Xa0hfvtF06qboNdhvuYXUku9ZrhZh3bSk8VXF0A/RuSLHpLsSV9Zqhl6';
                script.crossOrigin = 'anonymous';
                script.onload = boot;
                document.head.appendChild(script);
            } else {
                boot();
            }
        }
    }"
    x-on:remove-filter.window="if ($event.detail && $event.detail.field === '{{ $filter->getKey() }}' && instance) { instance.clear(); hasValue = false; $wire.set('tableFilters.{{ $filter->getKey() }}.from', ''); $wire.set('tableFilters.{{ $filter->getKey() }}.to', '') }"
    x-on:livewire-tables:clear-filters.window="if (instance) { instance.clear(); hasValue = false; } $wire.set('tableFilters.{{ $filter->getKey() }}.from', ''); $wire.set('tableFilters.{{ $filter->getKey() }}.to', '')"
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
        style="padding-left:2rem;padding-right:2.5rem"
        placeholder="{{ __('livewire-tables::messages.from') }} — {{ __('livewire-tables::messages.to_date') }}"
        readonly
    />
    <button
        type="button"
        x-show="hasValue"
        x-cloak
        x-on:click.stop="if (instance) instance.clear(); hasValue = false; $wire.set('tableFilters.{{ $filter->getKey() }}.from', ''); $wire.set('tableFilters.{{ $filter->getKey() }}.to', '')"
        style="position:absolute;right:0.5rem;top:0;bottom:0;display:flex;align-items:center;justify-content:center;padding:0 2px;border:none;background:none;cursor:pointer;color:#6b7280;z-index:2"
        onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#6b7280'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;pointer-events:none">
            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
        </svg>
    </button>
</div>
