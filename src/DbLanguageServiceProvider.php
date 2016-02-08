<?php

namespace Rhinodontypicus\DBLanguage;

use Illuminate\Support\ServiceProvider;

class DbLanguageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../resources/config/laravel-db-language.php', 'laravel-db-language');
        $this->app->singleton(DbLanguage::class, DbLanguage::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [DbLanguage::class];
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigration();
    }

    /**
     * Publish Migration
     */
    private function publishMigration()
    {
        if (!class_exists('CreateDbLanguageTables')) {
            $timestamp = date('Y_m_d_His', time());
            $localPath = __DIR__ . '/../resources/migrations/create_db_language_tables.php.stub';
            $appPath = database_path("migrations/{$timestamp}_create_db_language_tables.php");
            $this->publishes([$localPath => $appPath], 'migrations');
        }
    }

    /**
     * Publish Config
     */
    private function publishConfig()
    {
        $localPath = __DIR__ . '/../resources/config/laravel-db-language.php';
        $appPath = "{$this->app->configPath()}/laravel-db-language.php";
        $this->publishes([$localPath => $appPath], 'config');
    }
}
