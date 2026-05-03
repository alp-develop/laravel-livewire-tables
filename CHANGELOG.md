# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.2] - 2026-05-01

### Security

- **IDOR in bulk actions**: `getSelectedIds()` now intersects client `selectedIds` with the live server query, preventing forged IDs from targeting unauthorized records.
- **CSV formula injection in export headers**: Column labels now pass through `escapeCsvValue()` before `fputcsv()`.
- **XSS in ActionColumn icon**: `<script>`, `<iframe>`, `<object>`, `<embed>`, `<form>`, `<meta>`, `<link>`, `<base>`, and `<style>` tags are stripped; `on*=` event handlers and `javascript:`/`vbscript:`/`data:` URIs in `href`/`src`/`action` attributes are also removed before rendering.
- **DoS in DateRangeFilter**: Guard changed from `!== null` to `instanceof Carbon` — `Carbon::createFromFormat()` can return `false`, which would cause a fatal `TypeError` with the previous check.
- **Search/TextFilter input length limit**: `$search` and `TextFilter` values are now capped at 200 characters to prevent excessive LIKE wildcard queries.
- **`perPage` bypass**: perPage is validated against `$perPageOptions` at `State` build time in `render()`, not only in the Livewire hook.
- **Flatpickr CDN pinned with SRI**: All three date filter views now load `flatpickr@4.6.13` with SHA-384 `integrity` and `crossorigin="anonymous"` attributes.
- **SelectFilter out-of-allowlist values**: Single-select `apply()` now skips queries where the submitted value is not a key in `selectOptions`.
- **CSS injection via config colors**: Color values from `config('livewire-tables.colors.*')` and dark mode overrides are validated against a safe CSS character allowlist before output inside the `<style>` tag.
- **TOCTOU protection on bulk IDs**: `getSelectedIds()` uses `array_intersect` against the live filtered query — bulk IDs injected from the client that are not present in the current result set are silently dropped.
- **Sort field whitelist via session**: Sort fields restored from session cache are validated against the columns defined in `columns()`, preventing injected sort fields from reaching `SortStep`.
- **`selectedIds`, `excludedIds`, `selectAllPages` protected with `#[Locked]`**: These properties are now marked with Livewire's `#[Locked]` attribute, preventing direct client-side mutation via wire protocol.
- **SortStep rejects malformed field names**: Fields containing characters outside `[a-zA-Z0-9_.]` are now rejected entirely instead of being silently stripped.

### Performance

- **Filter resolution caching**: Added `resolveFilters()` with an in-request `$cachedFilters` cache. The `filters()` method was previously called up to 5 times per Livewire request. All internal calls now go through `resolveFilters()`.
- **FilterStep map pre-built in constructor**: `FilterStep` builds a `filterMap` keyed by filter key in its constructor — filter lookups are now O(1) instead of O(n) on every `apply()` call.
- **SortStep map pre-built in constructor**: `SortStep` builds a `columnMap` keyed by field in its constructor — column lookups are now O(1) instead of O(n) on every `apply()` call.
- **SearchStep searchable columns pre-computed**: Searchable columns are filtered once in the constructor via a readonly array, eliminating repeated `array_filter` on each apply.
- **SearchStep alias map cached**: `buildAliasMap()` result is cached per instance via `$cachedAliasMap ??=` — the regex-based map is built once instead of on every search.
- **Column caching (3-tier)**: `cachedColumns`, `cachedVisibleColumns`, and `cachedSearchableColumns` ensure `columns()` is called once per request.
- **`getFilterByKey()` O(1) via hash map**: `DataTableComponent` builds a `$cachedFilterMap` on first call — all subsequent `getFilterByKey()` and `resolveParentValue()` lookups are O(1).
- **HasExport reuses cached Engine**: `buildExportQuery()` now calls `$this->getEngine()->applySteps()` instead of constructing new `SearchStep`, `FilterStep`, and `SortStep` instances.
- **`buildSortChips()` O(n) instead of O(n×m)**: Now builds a `$columnByField` map once before iterating sort fields, eliminating the inner loop.
- **SelectFilter options cache**: `resolveOptions()` caches dependent filter callback results per value within the request lifecycle.
- **Session dirty tracking**: `dehydrate()` compares new state against the existing session before writing, avoiding unnecessary session writes on unchanged state.
- **Eager loading via `setEagerLoad()`**: Added `setEagerLoad(array $relations)` to `HasConfiguration`, allowing components to declare Eloquent relations to eager-load in `configure()`. Relations are applied in `render()` via `$query->with()`.

### Added

- **`Engine` is now non-final**: Removed `final` modifier from `Engine` to allow subclassing for custom pipeline orchestration.
- **`getEngine()` is now `protected`**: `getEngine()` and `$cachedEngine` changed from `private` to `protected`, enabling subclasses to override the Engine instance.
- **`setEagerLoad()` / `getEagerLoad()`**: New protected/public pair on `HasConfiguration` for declaring eager-loaded relations per component.

### Fixed

- **Bulk button Alpine flash**: `x-cloak` added to the bulk actions toggle container and `[x-cloak]{display:none!important}` rule added to `styles.blade.php`, preventing the button from briefly appearing with wrong CSS classes before Alpine initializes.

### Tests

- Added `SortStepTest`: field whitelist, regex sanitization, direction normalization.
- Added `LikeEscapeTest` extensions: 200-char truncation, field sanitization, custom search callback truncation passthrough.
- Added `FilterStepTest` extension: filter key whitelist validation.
- Added `SelectFilterNormalizeTest` extensions: integer key normalization, null value, type edge cases.
- Added `ActionColumnTest`: XSS injection rendered as escaped text, valid render.
- Added `BladeColumnSecurityTest`: raw HTML output by design, `e()` escape pattern, unescaped developer responsibility.
- Added `ValidationTest`: perPage bypass (2 tests), TOCTOU bulk ID protection (2 tests), sort field session injection.
- Added `ExportSecurityTest` extension: CSV tab/CR injection.
- Added `EngineApplyStepsTest` extension: Engine subclass extensibility.
- Added `IntegrationTest`: search+filter combined, bulk delete with active filter, bulk delete TOCTOU with filter, sort order, selectAllPages exclusions, `getFilterByKey` lookup.
- Added `HasConfigurationTest` (22 tests): setters/getters for CSS classes, debounce clamp, eagerLoad, perPageOptions, theme detection.
- Added `HasFiltersTest` (19 tests): clearFilters, removeFilter for all filter types, removeFilter cascade on dependent SelectFilters, applyFilter validation, filterHasActiveValue, getFilterValue.
- Added `UpdatedTableFiltersTest` (9 feature tests): updatedTableFilters via Livewire, selection reset, value normalization, dependent child filter clearing, clearFilters/removeFilter/applyFilter integration.
- Added `HasSearchAndSortingTest` (18 tests): hasSearch, clearSearch, updatedSearch truncation, sortBy toggle cycle, clearSort, clearSortField, isSortedBy, getSortDirection, getSortOrder.

## [1.2.1] - 2026-04-06

### Fixed

- **Filter initial values persist after Clear All**: Filters using `initialValue()` no longer re-apply their default value when the page is reloaded after clicking "Clear All". Changed the condition in `mount()` from `filterHasActiveValue()` (which treated empty strings/arrays as "no value") to `array_key_exists()` (which respects cached empty state from a previous clear).
- **Dark mode selector type**: Changed `dark_mode.selector` from CSS selector (`.lt-dark`) to session key (`lt-dark`) in `config/livewire-tables.php` and `demo/config/livewire-tables.php`. The selector is now used as a Laravel session key, not a CSS class.
- **Dark mode session-based detection**: `DataTableComponent::boot()` now reads dark mode state from the session (`session($selector)`) instead of relying on a `#[Reactive]` property passed from parent components. Removed `#[Reactive]` attribute and changed `$darkMode` type from `?bool` to `bool`.
- **Demo dark mode mechanism**: Replaced Livewire-dispatched `dark-mode-changed` event with browser-native `lt-dark-toggled` event in `demo.js`. Removed `onDarkModeChanged()` listener and `$darkMode` property from `DemoPage.php`. Removed `:dark-mode` bindings from Livewire component tags in `demo-page.blade.php`.
- **Alpine dark mode wrapper**: Added Alpine.js `x-data`/`x-on:lt-dark-toggled` wrapper in `table.blade.php` for instant client-side dark mode toggle without server round trip.
- **Dark mode chip/badge overrides**: Added missing dark mode CSS overrides for chips, badges, and interactive elements in `styles.blade.php`.
- **Demo package name**: Corrected `demo/composer.json` package name from `alvitres01/laravel-livewire-tabless` to `alp-develop/laravel-livewire-tables`.
- **Translation publish tag**: Corrected `--tag=livewire-tables-lang` to `--tag=livewire-tables-translations` in docs to match `LivewireTablesServiceProvider`.
- **Theming docs theme values**: Updated `docs/theming.md` to list `bootstrap-5`/`bootstrap-4` as primary config values with aliases, consistent with `docs/configuration.md`.

### Changed

- **`tableKey` is now a public `#[Locked]` property**: Changed from `protected` to `public` with Livewire's `#[Locked]` attribute. You can now pass `table-key` directly from Blade tags to isolate state when rendering multiple instances of the same table component: `<livewire:users-table table-key="users-active" />`.
- **Config publish is now required**: Updated installation docs to reflect that publishing `config/livewire-tables.php` is required, not optional.
- **README improvements**: Added "Configuration Reference" table describing every config option, added "Multiple Tables in the Same View" section with `table-key` usage examples.

### Added

- **Dark mode documentation**: New `docs/dark-mode.md` guide covering configuration, toggling, session detection, `$this->darkMode`, color presets, and cross-theme support.
- **Configuration docs**: Added `dark_mode` section, available themes table, and dark mode link to `docs/configuration.md`.
- **README install section**: Expanded with numbered steps (require, publish config, choose theme, Tailwind tip) and added dark mode doc to the documentation index.

## [1.2.0] - 2026-04-03

### Added

- **Laravel 13 support**: Full compatibility with Laravel 13.

### Security

- **CSV formula injection prevention in export**: Cell values starting with `=`, `+`, `-`, `@`, tab, or carriage return are now prefixed with a single quote to prevent formula execution when opening exported CSV files in Excel or Google Sheets.
- **XSS prevention in ImageColumn**: Image `src` attributes are now validated against an allowlist of safe URI schemes (`http://`, `https://`, `/`). Values using `javascript:`, `data:`, or other dangerous schemes are rejected and the image is not rendered.
- **HTML attribute injection prevention in ActionColumn**: The `wire:click` action and `class` attributes in action buttons are now escaped with `htmlspecialchars()` to prevent attribute breakout and XSS.
- **JavaScript context injection prevention in bulk checkboxes**: Row IDs in bulk action checkboxes now use `Js::from()` for safe JavaScript value encoding instead of raw string interpolation. Also uses dynamic `getKeyName()` instead of hardcoded `'id'`.
- **SearchStep field sanitization hardening**: The field sanitization in `applyFieldSearch()` no longer falls back to the unsanitized field name on `preg_replace` failure. If sanitization fails or produces an empty string, the field is skipped entirely.
- **SortStep field sanitization**: Sort field names are now sanitized with the same `[^a-zA-Z0-9_.]` regex as search fields before being passed to `orderBy()`, preventing SQL injection through crafted column identifiers.
- **Session state validation on load**: `loadStateFromCache()` now validates `sortFields`, `tableFilters`, and `hiddenColumns` against defined columns and filters when restoring from session, matching the validation already applied during `dehydrate()`.

---

## [1.1.1] - 2026-03-08

### Fixed

- **`QueryException` when sorting with `BladeColumn` auto-generated field**: `BladeColumn::sortable()` is now a no-op — marking a `BladeColumn` as sortable is silently ignored since it has no real DB column. Additionally, `resolveColumns()` now resets `BladeColumn::$bladeCounter` before each cache build, ensuring consistent `_blade_N` field IDs across multiple component instances and Octane environments where the static counter could accumulate between requests.
- **TypeError in `Filter::applyFilter()` when closure returns null**: The `applyFilter()` method now guards against user-defined filter closures that omit an explicit `return` statement. If the callback returns a non-`Builder` value (including `null`), the original `$query` is returned instead, preventing a fatal `TypeError: Return value must be of type Builder, null returned`.
- **Bulk action checkboxes styling conflicts with third-party CSS frameworks**: Changed bulk checkbox class from `form-check-input` (Bootstrap-specific) to `lt-bulk-checkbox` (livewire-tables-specific). This prevents style collisions with frameworks like AdminLTE, Argon, or any other theme that defines `.form-check-input` with conflicting styles. All themes (Tailwind, Bootstrap 5, Bootstrap 4) now use the dedicated `lt-bulk-checkbox` class with theme colors (primary color when checked) via CSS variables for consistent dark mode support.

---

## [1.1.0] - 2026-03-06

### Fixed

- **Column toggle dropdown behind BladeColumn cells**: The column visibility dropdown now renders above `BladeColumn` cells that use `position-relative` internally (e.g. action buttons, toggles). Fixed by ensuring the dropdown has an explicit `z-index` that takes precedence over positioned cell content.
- **TypeError on `$darkMode` during Livewire update**: Changed `$darkMode` property type from `bool` to `?bool` so Livewire can safely hydrate `null` (e.g. when a row is deleted and the component re-syncs state through middleware), preventing a fatal `TypeError: Cannot assign null to property of type bool`.

### Added

- **11 new bundled languages**: Added translations for French (`fr`), German (`de`), Italian (`it`), Dutch (`nl`), Polish (`pl`), Russian (`ru`), Chinese Simplified (`zh`), Japanese (`ja`), Korean (`ko`), Turkish (`tr`), and Indonesian (`id`). Total bundled locales: 14. Publishable via `php artisan vendor:publish --tag=livewire-tables-translations`.
- **Demo language selector**: The demo header now includes a language switcher showing all 14 supported locales. Selection is stored in session and applies on next render via Livewire. The selector button label and dropdown item names are fully translated via `languages.php` per locale.
- **Demo full i18n**: All demo UI text — tab names, section titles, descriptions, stat card labels, modal fields, buttons, and placeholders — is now driven by `__('demo.key')` translations for all 14 locales.
- **Demo country selector**: The "Add Catalog Item" modal country field is now a `<select>` dropdown with 35 countries, replacing the free-text input.

---

## [1.0.3] - 2026-03-05

### Fixed

- **Join column display with custom SELECT alias**: Columns defined with dot notation (e.g. `TextColumn::make('users.email')`) now correctly display values when the SELECT query uses a custom alias (e.g. `users.email as user_email`). Previously, display resolution only worked when the alias matched the auto-derived `table_column` pattern. The engine now falls back to the bare column name when the derived alias key is absent from the result row.

---

## [1.0.2] - 2026-03-05

### Fixed

- **Flatpickr calendar stays open**: Flatpickr now closes when clicking outside the filter panel or when opening another dropdown filter.
- **Flatpickr on mobile**: Date, date range, and multi-date filters now force Flatpickr (`disableMobile: true`) instead of falling back to native date inputs on mobile devices.
- **Filter select dropdown overflow**: Select dropdowns inside the filter panel no longer clip nested options (removed `overflow-y-auto` from Tailwind filter-dropdown).
- **DateRange closes filter panel (BS4/BS5)**: Clicking the Flatpickr calendar no longer triggers `@click.outside` and closes the filter panel.
- **Select/MultiSelect/Boolean dark mode**: Dropdown background, borders, search input, and option hover/active colors now use CSS variables (`--lt-bg-card`, `--lt-border`, `--lt-text`, `--lt-opt-*`) for proper dark mode support across all themes.
- **Select arrow dark mode**: Native select arrows (`.lt-select`, `.form-select`, `.custom-select`) now use a light SVG fill in dark mode instead of the default dark fill. Fixed `background` shorthand resetting arrow positioning.
- **Pagination overflow on mobile**: Page numbers are reduced on small screens — only ±2 pages around current, plus first/last page are shown (via `lt-page-hide-mobile` class).

### Changed

- **Mobile responsive toolbar**: Filter, column, and bulk action dropdowns display as full-width overlays on mobile. Per-page, selection bar buttons, and all toolbar items stretch properly. Applied to Tailwind, Bootstrap 5, and Bootstrap 4.
- **Demo page responsive**: Theme switcher bar wraps, tab badges hidden, stat cards smaller, modal form stacks to single column, reduced padding on mobile.

---

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
