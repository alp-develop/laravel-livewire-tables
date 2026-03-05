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
            <button class="lt-dark-toggle" onclick="ltToggleDark()" title="Toggle dark mode"></button>
        </div>
    </div>

    @yield('content')

    @livewireScripts

    {{-- Demo-specific JS (dark mode toggle, icon management) --}}
    <script src="{{ asset('js/demo.js') }}"></script>
</body>
</html>
