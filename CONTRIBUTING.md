# Contributing

Thank you for considering contributing to Livewire Tables.

## Development Setup

1. Fork and clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`

## Workflow

1. Create a branch from `main` for your change
2. Make your changes following the project architecture
3. Add or update tests as needed
4. Ensure all checks pass:
   - `composer test` — All tests pass
   - `composer analyse` — PHPStan level 8
   - `composer format` — Laravel Pint code style
5. Submit a pull request to `main`

## Architecture Rules

- **Core Engine** (`src/Core/`) — Pure PHP. No Livewire, Blade, or facades.
- **Livewire Adapter** (`src/Livewire/`) — Thin layer. State + delegation to Engine.
- **Theme System** (`src/Themes/`) — Driver pattern via `ThemeContract`.
- Do not mix responsibilities between layers.
- Do not modify existing contracts — extend them instead.
- Do not store closures in public Livewire properties.

## Code Standards

- PHP 8.1+ compatible — no `readonly class`, DNF types, or `#[Override]`
- Strict types in all files
- No comments in code — use clear, self-documenting names
- Final classes by default, abstract only when designed for extension
- Readonly constructor properties where possible (per-property, not class-level)

## Tests

- Use Pest with Orchestra Testbench
- Place unit tests in `tests/Unit/`, feature tests in `tests/Feature/`
- Test components extend `DataTableComponent` for integration testing
- All new features must include tests

## Pull Request Guidelines

- One feature or fix per PR
- Include a clear description of what changed and why
- Reference any related issues
- Ensure CI passes before requesting review
