<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | The CSS framework theme used to render the tables.
    |
    | Supported: "tailwind", "bootstrap-5", "bootstrap-4"
    |            Aliases: "bootstrap5", "bootstrap4", "bootstrap" (resolves to bootstrap-5)
    |
    */

    'theme' => 'tailwind',

    /*
    |--------------------------------------------------------------------------
    | Primary Color Palette
    |--------------------------------------------------------------------------
    |
    | Customize the primary color used across all table components (buttons,
    | checkboxes, pagination, filter badges, selection bar, etc.).
    |
    | These map to CSS custom properties --lt-primary-50 through --lt-primary-700.
    | Works identically for both Tailwind and Bootstrap themes.
    |
    | Presets:  Teal (default), Indigo, Sky, Purple, Rose, Amber
    |   Teal:   50:#f0fdfa 100:#ccfbf1 200:#99f6e4 400:#2dd4bf 500:#14b8a6 600:#0d9488 700:#0f766e
    |   Indigo: 50:#eef2ff 100:#e0e7ff 200:#c7d2fe 400:#818cf8 500:#6366f1 600:#4f46e5 700:#4338ca
    |   Sky:    50:#f0f9ff 100:#e0f2fe 200:#bae6fd 400:#38bdf8 500:#0ea5e9 600:#0284c7 700:#0369a1
    |   Purple: 50:#faf5ff 100:#f3e8ff 200:#e9d5ff 400:#c084fc 500:#a855f7 600:#9333ea 700:#7e22ce
    |   Rose:   50:#fff1f2 100:#ffe4e6 200:#fecdd3 400:#fb7185 500:#f43f5e 600:#e11d48 700:#be123c
    |   Amber:  50:#fffbeb 100:#fef3c7 200:#fde68a 400:#fbbf24 500:#f59e0b 600:#d97706 700:#b45309
    |
    */

    'colors' => [
        '50' => '#f0fdfa',
        '100' => '#ccfbf1',
        '200' => '#99f6e4',
        '400' => '#2dd4bf',
        '500' => '#14b8a6',
        '600' => '#0d9488',
        '700' => '#0f766e',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dark Mode
    |--------------------------------------------------------------------------
    |
    | Enable dark mode support for the data tables. When enabled, the package
    | injects dark CSS rules scoped to the 'lt-dark' class. Toggle dark mode
    | by adding/removing 'lt-dark' on <html> and dispatching the browser event
    | 'lt-dark-toggled'. The table uses Alpine.js to react instantly without
    | any server round trip.
    |
    | 'enabled'  — Whether dark mode support is active (default: false).
    | 'selector' — Session key used to detect dark mode state (default: 'lt-dark').
    |              Store a truthy value in this session key to activate dark mode
    |              server-side: session(['lt-dark' => true])
    | 'colors'   — Customize the dark mode palette:
    |             bg         → Page / outer background
    |             bg-card    → Card / panel / table container background
    |             bg-subtle  → Subtle backgrounds (thead, stripes, hover)
    |             border     → Borders and dividers
    |             text       → Primary text color
    |             text-muted → Secondary / muted text color
    |
    | Presets:
    |   Slate (default): bg:#0f172a bg-card:#1e293b bg-subtle:#334155 border:#334155 text:#f1f5f9 text-muted:#94a3b8
    |   Zinc:            bg:#18181b bg-card:#27272a bg-subtle:#3f3f46 border:#3f3f46 text:#fafafa  text-muted:#a1a1aa
    |   Neutral:         bg:#171717 bg-card:#262626 bg-subtle:#404040 border:#404040 text:#fafafa  text-muted:#a3a3a3
    |
    */

    'dark_mode' => [
        'enabled' => false,
        'selector' => 'lt-dark',
        'colors' => [
            'bg' => '#0f172a',
            'bg-card' => '#1e293b',
            'bg-subtle' => '#334155',
            'border' => '#334155',
            'text' => '#f1f5f9',
            'text-muted' => '#94a3b8',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Debounce
    |--------------------------------------------------------------------------
    |
    | The debounce time in milliseconds for the search input. This delays
    | the search request until the user stops typing for the given time.
    |
    | Recommended: 300-500 for a smooth experience.
    |
    */

    'search_debounce' => 300,

    /*
    |--------------------------------------------------------------------------
    | Component Namespace
    |--------------------------------------------------------------------------
    |
    | The subdirectory inside app/Livewire/ where the make:livewiretable command
    | will generate new table components.
    |
    | Example: "Tables" generates in app/Livewire/Tables/
    |          "DataTables" generates in app/Livewire/DataTables/
    |
    */

    'component_namespace' => 'Tables',

];
