<?php

namespace MohamedAhmed\LaravelPolyLang;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class LaravelPolyLangServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // publish config
        $this->publishes([
            __DIR__.'/config/polylang.php' => config_path('polylang.php'),
        ], 'config');

        // publish migration
        if (!class_exists('CreateTranslationsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_translations_table.php' =>
                    database_path("migrations/{$timestamp}_create_translations_table.php"),
            ], 'migrations');

            if (!Schema::hasTable('translations')) {
                $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            }
        }

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/polylang.php', 'polylang');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \MohamedAhmed\LaravelPolyLang\Console\Commands\UninstallCommand::class,
            ]);
        }
    }
}
