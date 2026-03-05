<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | The CSS framework theme used to render the tables.
    |
    | Supported: "tailwind", "bootstrap5", "bootstrap4", "bootstrap" (alias for bootstrap5)
    |
    */

    'theme' => 'bootstrap5',

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

    /*
    |--------------------------------------------------------------------------
    | Primary Color Palette
    |--------------------------------------------------------------------------
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
    | Enable dark mode support for the tables. When enabled, the package
    | injects CSS that adapts to the configured selector (e.g. .lt-dark
    | on <html>). If disabled, the table stays in light mode regardless.
    |
    */

    'dark_mode' => [
        'enabled' => true,
        'selector' => '.lt-dark',
    ],

];
