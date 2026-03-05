# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-03-05

### Fixed

- **Filter panel closes on interaction in hidden containers**: Filter dropdowns now remain open when interacting with filters inside tabs, modals, or accordions. Uses `Livewire.hook('commit')` to persist Alpine state across component re-renders triggered by parent morphs.
- **Dependent filter clearing**: `removeFilter()` now properly clears child filters that depend on the removed parent filter. Fixed comparison bug in `updatedTableFilters()` where field name was incorrectly compared to filter key.
- **Text filter debounce**: Text filters now use `wire:model.live.debounce.500ms` instead of instant updates, with a clear (X) button.
- **Initial filter dispatch**: Tables with `initialValue()` filters now dispatch `table-filters-applied` on mount so parent components receive the initial filter state.
- **Multi-select dropdown persistence**: Multi-select filter dropdowns now stay open after toggling options, using the same state persistence mechanism.
- **Only one dropdown open at a time**: Opening a select/multi-select/boolean filter dropdown now closes any other open dropdown via `lt-dropdown-opened` event.
- **Clear All filters bug**: Clicking "Clear All" now correctly clears all filters on the first click.
- **Filter dropdown closes on first apply**: The filter dropdown no longer closes when applying the first filter on a page. Added `wire:key` to all filter blade components.

### Changed

- **Clear All button style**: Now uses the same neutral theme style as the Columns button instead of the red danger style.
- **Select dropdown height**: Increased max-height of select/multi-select/boolean dropdowns from 11rem to 14.3rem (30% taller).
- **Filter panel width**: Filter panel now uses `min-width:22rem` for a more comfortable layout.
- **Bootstrap 4 & 5 toolbar**: Removed `card-header` class, now uses `rounded-top` with `border-bottom` for cleaner styling.

---

## [1.0.0] - 2026-03-04

### Added

**Core**
- Pipeline architecture: `SearchStep`, `FilterStep`, `SortStep` applied in sequence per request
- Immutable `State` value object carrying search, filters, sort fields, per-page, and page
- `Engine` processes the pipeline and returns paginated results
- `Compat` utility for runtime PHP, Laravel, and Livewire version detection

**Column Types**
- `TextColumn` — plain text with optional format closure
- `BooleanColumn` — yes/no with visual badge rendering
- `DateColumn` — date display with configurable format string
- `ImageColumn` — image display with optional lightbox preview
- `ActionColumn` — row action buttons with label, CSS class, and JS action callback
- `BladeColumn` — custom Blade view or render closure per cell
- Computed columns via `TextColumn::make()` (no field required) with `render(Closure)` support
- `sortable()`, `searchable()`, `label()`, `format()`, `headerClass()`, `cellClass()`, `hidden()` on all columns
- Column visibility toggle at runtime

**Filter Types**
- `TextFilter` — free-text `LIKE` search
- `SelectFilter` — single or multiple selection; supports `searchable()` and parent-child dependency with `dependsOn()` / `dependsOnValues()`
- `BooleanFilter` — true/false toggle
- `NumberFilter` — numeric input with min, max, and step constraints
- `NumberRangeFilter` — min/max range with two inputs
- `DateFilter` — single date picker (Flatpickr)
- `DateRangeFilter` — from/to date range (Flatpickr)
- `MultiDateFilter` — multiple individual dates (Flatpickr)
- Custom filter logic via `filter(Closure)` on any filter type
- Initial filter values via `defaultValue()`
- Active filter chips with per-filter and clear-all removal

**Pagination & Per-Page**
- Laravel native pagination with configurable per-page options
- `setDefaultPerPage()`, `setPerPageOptions()`, `setPerPageVisibility()`
- Per-page selector rendered in toolbar

**Sorting**
- Single and multi-column sorting
- Visual sort chips with active direction indicator
- `setDefaultSort()`, `setDefaultSortDirection()`
- Sort state persisted in session

**Search**
- Global full-text search across all `searchable()` columns
- Configurable debounce via `setSearchDebounce()`
- Auto-resolved SQL aliases in joined column searches
- `clearSearch()` resets search and dispatches filter event

**Bulk Actions**
- Exclusion-based selection model: select-all stores exclusions instead of IDs for large datasets
- Per-page selection bar with Select page / Deselect page / Select all / Deselect all
- `getSelectedIds()` resolves the correct ID list regardless of selection mode
- Bulk action methods registered via `bulkActions()` array
- `exportCsvAuto` built-in bulk action for instant CSV download

**CSV Export**
- `exportCsvAuto` action generates CSV from all visible text/date/boolean columns
- Custom export via `exportCsv(Closure $headings, Closure $row)` for full control
- Streamed response — no temporary files

**Events & Lifecycle Hooks**
- `table-filters-applied` dispatched on every filter change, filter removal, clear-all, and search change; payload: `tableKey`, `filters`, `search`
- External table refresh via `{tableKey}-refresh` or global `livewire-tables:refresh` events
- Custom refresh event name via protected `$refreshEvent` property
- Custom Livewire event listeners via `listeners()` method
- Lifecycle hooks: `onQuerying(Builder)`, `onQueried(Collection)`, `onRendering()`, `onRendered()`

**Toolbar Slots**
- Six injection points: `beforeToolbar`, `afterToolbar`, `beforeSearch`, `afterSearch`, `beforeFilters`, `afterFilters`
- Inject any Blade content via `setSlot(string $slot, string $view, array $data)`

**Themes**
- `TailwindTheme` — default theme using self-contained `lt-*` CSS classes (no Tailwind scanning required)
- `Bootstrap5Theme` — Bootstrap 5 compatible theme
- `Bootstrap4Theme` — Bootstrap 4 compatible theme
- Custom theme support via `ThemeContract`
- CSS custom properties (`--lt-primary-50` through `--lt-primary-700`) for primary color customization
- Dark mode support via reactive `$darkMode` prop passed from parent component
- `ThemeManager` driver registry; switch theme via config or per table with `setTheme()`
- Per-table and per-filter CSS class overrides via `headerClass()`, `cellClass()`, `setTableClass()`, etc.
- Dynamic row classes via `setTrClass(Closure)` or string

**State Persistence**
- Search, filters, sort fields, and pagination page stored in session per table key
- State restored automatically on component boot

**Artisan Generator**
- `php artisan make:livewiretable UsersTable User` generates ready-to-edit scaffold
- Subdirectory support: `make:livewiretable Admin/UsersTable User`
- Smart imports: only column and filter types actually used are imported

**Internationalization**
- Translation keys for all UI labels (search, filters, pagination, bulk actions, export)
- Bundled languages: English (`en`), Spanish (`es`), Portuguese (`pt`)
- Publishable via `php artisan vendor:publish --tag=livewire-tables-lang`

**Developer Experience**
- PHPStan level 8 static analysis
- Laravel Pint code style enforcement
- Full test suite with Pest and Orchestra Testbench (184 tests, 479 assertions)
- Docker multi-version testing: 9 matrix combinations via `docker/test.sh`
- GitHub Actions CI covering PHP 8.1–8.4, Laravel 10–12, Livewire 3–4

**Compatibility**
- PHP 8.1, 8.2, 8.3, 8.4
- Laravel 10.x, 11.x, 12.x
- Livewire 3.x, 4.x
