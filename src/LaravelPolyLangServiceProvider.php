<?php

namespace MohamedAhmed\LaravelPolyLang;

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
            $this->publishes([
                __DIR__.'/../database/migrations/2024_01_01_000000_create_translations_table.php' =>
                    database_path('migrations/' . date('Y_m_d_His', time()) . '_create_translations_table.php'),
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/polylang.php', 'polylang');
    }
}
