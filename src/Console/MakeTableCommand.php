<?php

declare(strict_types=1);

namespace Livewire\Tables\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class MakeTableCommand extends Command
{
    protected $signature = 'make:livewiretable {name? : The name of the table component (supports slashes for subdirectories)} {model? : The model class to use} {--model= : The model class to use (alternative)}';

    protected $description = 'Create a new Livewire DataTable component';

    public function __construct(
        private readonly Filesystem $files,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $nameArg = $this->argument('name');
        $nameInput = is_string($nameArg) ? $nameArg : $this->askForName();

        $modelArg = $this->argument('model');
        $modelOpt = $this->option('model');
        $modelInput = is_string($modelArg) ? $modelArg : (is_string($modelOpt) ? $modelOpt : $this->askForModel());

        $modelClass = $this->resolveModelClass($modelInput);

        if (! class_exists($modelClass)) {
            $this->error("Model [{$modelClass}] not found.");

            return self::FAILURE;
        }

        $columns = $this->introspectColumns($modelClass);

        $nameInput = str_replace('/', '\\', $nameInput);
        $segments = explode('\\', $nameInput);
        $className = Str::studly(array_pop($segments));
        $subPath = implode('\\', array_map(fn (string $s): string => Str::studly($s), $segments));

        $namespace = $this->resolveNamespace($subPath);
        $path = $this->resolvePath($subPath, $className);

        if ($this->files->exists($path)) {
            $this->error("Component [{$className}] already exists at [{$path}].");

            return self::FAILURE;
        }

        $this->files->ensureDirectoryExists(dirname($path));

        $imports = $this->resolveImports($columns, $modelClass);
        $stub = $this->buildStub($className, $namespace, $modelClass, $columns, $imports);

        $this->files->put($path, $stub);

        $this->info("Table component [{$className}] created successfully.");
        $this->line("  <fg=gray>{$path}</>");

        return self::SUCCESS;
    }

    private function askForName(): string
    {
        return $this->ask('What should the table component be named?', 'UsersTable');
    }

    private function askForModel(): string
    {
        return $this->ask('Which model should this table use?', 'User');
    }

    private function resolveModelClass(string $model): string
    {
        if (str_contains($model, '\\')) {
            return $model;
        }

        return 'App\\Models\\'.Str::studly($model);
    }

    private function resolveNamespace(string $subPath): string
    {
        $subfolder = trim((string) config('livewire-tables.component_namespace', 'Tables'));
        $parts = array_filter(['App', 'Livewire', $subfolder, $subPath], fn (string $p): bool => $p !== '');

        return implode('\\', $parts);
    }

    private function resolvePath(string $subPath, string $className): string
    {
        $subfolder = trim((string) config('livewire-tables.component_namespace', 'Tables'));
        $parts = array_filter([$subfolder, $subPath], fn (string $p): bool => $p !== '');
        $directory = implode(DIRECTORY_SEPARATOR, array_map(
            fn (string $p): string => str_replace('\\', DIRECTORY_SEPARATOR, $p),
            $parts,
        ));

        $basePath = $directory !== '' ? "Livewire/{$directory}" : 'Livewire';

        return app_path("{$basePath}/{$className}.php");
    }

    /** @return array<int, array{name: string, type: string}> */
    private function introspectColumns(string $modelClass): array
    {
        /** @var Model $model */
        $model = new $modelClass;
        $table = $model->getTable();

        if (! Schema::hasTable($table)) {
            return [];
        }

        $dbColumns = Schema::getColumns($table);
        $hidden = $model->getHidden();
        $skip = ['id', 'password', 'remember_token', 'email_verified_at', 'created_at', 'updated_at', 'deleted_at'];

        $result = [];

        foreach ($dbColumns as $column) {
            $name = $column['name'];

            if (in_array($name, $hidden, true) || in_array($name, $skip, true)) {
                continue;
            }

            $result[] = [
                'name' => $name,
                'type' => $this->mapColumnType($column['type_name']),
            ];
        }

        return $result;
    }

    private function mapColumnType(string $dbType): string
    {
        $dbType = strtolower($dbType);

        return match (true) {
            in_array($dbType, ['boolean', 'tinyint', 'bool'], true) => 'boolean',
            in_array($dbType, ['date', 'datetime', 'timestamp'], true) => 'date',
            default => 'text',
        };
    }

    /**
     * @param  array<int, array{name: string, type: string}>  $columns
     * @return array<int, string>
     */
    private function resolveImports(array $columns, string $modelClass): array
    {
        $imports = [
            $modelClass,
            'Illuminate\\Database\\Eloquent\\Builder',
        ];

        $types = array_unique(array_column($columns, 'type'));

        foreach ($types as $type) {
            $imports[] = match ($type) {
                'boolean' => 'Livewire\\Tables\\Columns\\BooleanColumn',
                'date' => 'Livewire\\Tables\\Columns\\DateColumn',
                default => 'Livewire\\Tables\\Columns\\TextColumn',
            };
        }

        if (count($types) === 0) {
            $imports[] = 'Livewire\\Tables\\Columns\\TextColumn';
        }

        $hasBooleanFilter = false;
        foreach ($columns as $column) {
            if ($column['type'] === 'boolean') {
                $hasBooleanFilter = true;
                break;
            }
        }

        if ($hasBooleanFilter) {
            $imports[] = 'Livewire\\Tables\\Filters\\BooleanFilter';
        }

        $imports[] = 'Livewire\\Tables\\Livewire\\DataTableComponent';

        sort($imports);

        return array_unique($imports);
    }

    /**
     * @param  array<int, array{name: string, type: string}>  $columns
     * @param  array<int, string>  $imports
     */
    private function buildStub(string $className, string $namespace, string $modelClass, array $columns, array $imports): string
    {
        $modelShort = class_basename($modelClass);
        $columnsCode = $this->generateColumnsCode($columns);
        $filtersCode = $this->generateFiltersCode($columns);
        $importsCode = implode("\n", array_map(fn (string $import): string => "use {$import};", $imports));

        return <<<PHP
        <?php

        namespace {$namespace};

        {$importsCode}

        class {$className} extends DataTableComponent
        {
            public function configure(): void
            {
                \$this->setDefaultPerPage(10);
            }

            public function query(): Builder
            {
                return {$modelShort}::query();
            }

            public function columns(): array
            {
                return [
        {$columnsCode}        ];
            }

            public function filters(): array
            {
                return [
        {$filtersCode}        ];
            }
        }

        PHP;
    }

    /** @param array<int, array{name: string, type: string}> $columns */
    private function generateColumnsCode(array $columns): string
    {
        $lines = [];

        foreach ($columns as $column) {
            $label = Str::headline($column['name']);
            $columnClass = match ($column['type']) {
                'boolean' => 'BooleanColumn',
                'date' => 'DateColumn',
                default => 'TextColumn',
            };

            $chain = "            {$columnClass}::make('{$column['name']}')\n";
            $chain .= "                ->label('{$label}')\n";
            $chain .= '                ->sortable()';

            if ($column['type'] === 'text') {
                $chain .= "\n                ->searchable()";
            }

            $chain .= ",\n\n";

            $lines[] = $chain;
        }

        return implode('', $lines);
    }

    /** @param array<int, array{name: string, type: string}> $columns */
    private function generateFiltersCode(array $columns): string
    {
        $lines = [];

        foreach ($columns as $column) {
            if ($column['type'] === 'boolean') {
                $label = Str::headline($column['name']);
                $lines[] = "            BooleanFilter::make('{$column['name']}')\n                ->label('{$label}'),\n\n";
            }
        }

        return implode('', $lines);
    }
}
