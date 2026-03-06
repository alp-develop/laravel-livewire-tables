<div class="{{ $classes['per-page-wrapper'] }}"
    x-data="{ open: false, hovered: null }"
    @click.outside="open = false"
    style="position:relative"
>
    <button
        type="button"
        x-on:click.stop="open = !open"
        class="{{ $classes['per-page-select'] }}"
        style="text-align:left;cursor:pointer;min-width:4.5rem;display:flex;align-items:center;justify-content:space-between;gap:0.5rem"
    >
        <span>{{ $this->perPage }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;color:var(--lt-text-muted,#9ca3af);flex-shrink:0">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
        </svg>
    </button>
    <div x-show="open" x-cloak x-transition @click.stop
        style="position:absolute;right:0;top:100%;z-index:9999;margin-top:0.25rem;background:var(--lt-bg-card,#fff);border:1px solid var(--lt-border,#e5e7eb);border-radius:0.5rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);min-width:4.5rem;overflow:hidden;color:var(--lt-text,inherit)"
    >
        @foreach($this->getPerPageOptions() as $option)
            <div
                x-on:click="$wire.set('perPage', {{ $option }}); open = false"
                x-on:mouseenter="hovered = {{ $option }}"
                x-on:mouseleave="hovered = null"
                style="transition:background 0.1s;color:var(--lt-text,inherit)"
                :style="$wire.perPage == {{ $option }}
                    ? 'background:var(--lt-opt-active);color:var(--lt-opt-active-text);font-weight:600'
                    : (hovered == {{ $option }} ? 'background:var(--lt-opt-hover)' : '')"
            >
                <div style="padding:0.4rem 0.75rem;font-size:0.875rem;cursor:default;user-select:none">{{ $option }}</div>
            </div>
        @endforeach
    </div>
</div>
