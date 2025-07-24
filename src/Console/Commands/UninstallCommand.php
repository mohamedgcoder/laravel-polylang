<?php

namespace MohamedAhmed\LaravelPolyLang\Console\Commands;

use Illuminate\Console\Command;

class UninstallCommand extends Command
{
    protected $signature = 'polylang:uninstall';
    protected $description = 'Remove polylang config and migration files';

    public function handle(): void
    {
        $config = config_path('polylang.php');
        $migration = collect(glob(database_path('migrations/*_create_translations_table.php')))->first();

        if (file_exists($config)) {
            unlink($config);
            $this->info("Deleted config: $config");
        }

        if ($migration && file_exists($migration)) {
            unlink($migration);
            $this->info("Deleted migration: $migration");
        }

        $this->info('Polylang package cleanup complete.');
    }
}
