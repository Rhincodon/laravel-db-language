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
        $this->app->singleton('db.language', 'Rhinodontypicus\DBLanguage\Language');
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['db.language'];
    }
}