<?php

declare(strict_types=1);

namespace Livewire\Tables;

use Illuminate\Support\ServiceProvider;
use Livewire\Tables\Console\MakeTableCommand;
use Livewire\Tables\Themes\ThemeManager;

final class LivewireTablesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/livewire-tables.php', 'livewire-tables');

        $this->app->singleton(ThemeManager::class, function (): ThemeManager {
            $theme = config('livewire-tables.theme', 'tailwind');

            return new ThemeManager(active: is_string($theme) ? $theme : 'tailwind');
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-tables');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'livewire-tables');

        if ($this->app->runningInConsole()) {
            $this->commands([MakeTableCommand::class]);

            $this->publishes([
                __DIR__.'/../config/livewire-tables.php' => config_path('livewire-tables.php'),
            ], 'livewire-tables-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/livewire-tables'),
            ], 'livewire-tables-views');

            $this->publishes([
                __DIR__.'/../lang' => $this->app->langPath('vendor/livewire-tables'),
            ], 'livewire-tables-translations');
        }
    }
}
