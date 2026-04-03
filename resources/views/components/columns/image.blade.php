@php
    $src = $column->resolveValue($row);
    $alt = $column->getAltField() ? data_get($row, $column->getAltField()) : '';
    $safeSrc = is_string($src) && (str_starts_with($src, 'http://') || str_starts_with($src, 'https://') || str_starts_with($src, '/')) ? $src : null;
@endphp
@if($safeSrc)
    <div x-data="{ showLightbox: false }" style="display:flex;align-items:center;justify-content:center">
        <img
            src="{{ $safeSrc }}"
            alt="{{ $alt }}"
            style="max-height:64px;width:auto;border-radius:6px;cursor:pointer;display:block"
            loading="lazy"
            @click="showLightbox = true"
        />
        <template x-teleport="body">
            <div
                x-show="showLightbox"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="showLightbox = false"
                style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.85);backdrop-filter:blur(4px)"
            >
                <div
                    @click="showLightbox = false"
                    style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;cursor:pointer"
                >
                    <img
                        src="{{ $safeSrc }}"
                        alt="{{ $alt }}"
                        @click.stop
                        style="max-width:90vw;max-height:90vh;border-radius:8px;box-shadow:0 25px 50px rgba(0,0,0,0.5);cursor:default"
                    />
                </div>
                <button
                    @click="showLightbox = false"
                    type="button"
                    style="position:absolute;top:1rem;right:1rem;z-index:1;color:white;background:rgba(255,255,255,0.15);border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s"
                    onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
@endif
