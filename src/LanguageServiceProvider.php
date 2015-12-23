<?php

namespace Rhinodontypicus\DBLanguage;

use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
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
        $this->app->singleton('db.language', Language::class);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('/migrations')
        ], 'migrations');
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['db.language'];
    }
}
