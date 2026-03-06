<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livewire Tables — Demo ({{ ucfirst($theme) }})</title>

    {{-- Apply dark mode BEFORE any CSS loads to prevent flash --}}
    <script>
        if (localStorage.getItem('lt-dark') === '1') {
            document.documentElement.classList.add('lt-dark', 'dark');
        }
    </script>

    @if(str_starts_with($theme, 'bootstrap'))
        @if($theme === 'bootstrap4')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
        @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        @endif
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    @endif

    {{-- Demo-specific CSS (theme bar, stat cards, section headers, color variants) --}}
    <link rel="stylesheet" href="{{ asset('css/demo.css') }}">

    @livewireStyles
</head>
<body style="background:var(--lt-bg);min-height:100vh;margin:0;transition:background .2s">

    {{-- ═══════════════ THEME SWITCHER BAR ═══════════════ --}}
    <div class="lt-theme-bar">
        <div class="lt-theme-inner" style="max-width:80rem;margin:0 auto;padding:0 1.5rem">
            <a href="/tailwind" class="lt-theme-btn {{ $theme === 'tailwind' ? 'lt-active' : '' }}">Tailwind</a>
            <a href="/bootstrap5" class="lt-theme-btn {{ $theme === 'bootstrap5' ? 'lt-active' : '' }}">Bootstrap 5</a>
            <a href="/bootstrap4" class="lt-theme-btn {{ $theme === 'bootstrap4' ? 'lt-active' : '' }}">Bootstrap 4</a>
            <span class="lt-theme-sep"></span>
            {{-- ── Language selector ── --}}
            <div class="lt-lang-wrap" style="position:relative">
                <button class="lt-theme-btn" onclick="this.nextElementSibling.classList.toggle('lt-lang-open')" title="Language">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;margin-right:.3rem;vertical-align:-.1em"><path d="M21.721 12.752a9.711 9.711 0 0 0-.945-5.003 12.754 12.754 0 0 1-4.339 2.708 18.991 18.991 0 0 1-.214 4.772 17.165 17.165 0 0 0 5.498-2.477ZM14.634 15.55a17.324 17.324 0 0 0 .332-4.647c-.952.227-1.945.347-2.966.347-1.021 0-2.014-.12-2.966-.347a17.515 17.515 0 0 0 .332 4.647 17.385 17.385 0 0 0 5.268 0ZM9.772 17.119a18.963 18.963 0 0 0 4.456 0A17.182 17.182 0 0 1 12 21.724a17.18 17.18 0 0 1-2.228-4.605ZM7.777 15.23a18.87 18.87 0 0 1-.214-4.774 12.753 12.753 0 0 1-4.34-2.708 9.711 9.711 0 0 0-.944 5.004 17.165 17.165 0 0 0 5.498 2.477ZM21.356 14.752a9.765 9.765 0 0 1-7.478 6.817 18.64 18.64 0 0 0 1.988-4.718 18.627 18.627 0 0 0 5.49-2.098ZM2.644 14.752c1.682.971 3.53 1.688 5.49 2.099a18.64 18.64 0 0 0 1.988 4.718 9.765 9.765 0 0 1-7.478-6.816ZM13.878 2.43a9.755 9.755 0 0 1 6.116 3.986 11.267 11.267 0 0 1-3.746 2.504 18.63 18.63 0 0 0-2.37-6.49ZM12 2.276a17.152 17.152 0 0 1 2.805 7.121c-.897.23-1.837.353-2.805.353-.968 0-1.908-.122-2.805-.353A17.151 17.151 0 0 1 12 2.276ZM10.122 2.43a18.629 18.629 0 0 0-2.37 6.49 11.266 11.266 0 0 1-3.746-2.504 9.754 9.754 0 0 1 6.116-3.985Z"/></svg>
                    {{ __('demo.lang_name') }}
                </button>
                <div class="lt-lang-dropdown">
                    @foreach(['en','es','pt','fr','de','it','nl','pl','ru','zh','ja','ko','tr','id'] as $code)
                        <button
                            type="button"
                            onclick="Livewire.dispatch('set-language', {value: '{{ $code }}'}); this.closest('.lt-lang-dropdown').classList.remove('lt-lang-open')"
                            class="lt-lang-item {{ ($language ?? 'en') === $code ? 'lt-lang-active' : '' }}"
                        >{{ __('languages.'.$code) }}</button>
                    @endforeach
                </div>
            </div>
            <span class="lt-theme-sep"></span>
            <button class="lt-dark-toggle" onclick="ltToggleDark()" title="Toggle dark mode"></button>
        </div>
    </div>

    @yield('content')

    @livewireScripts

    {{-- Demo-specific JS (dark mode toggle, icon management) --}}
    <script src="{{ asset('js/demo.js') }}"></script>
</body>
</html>
