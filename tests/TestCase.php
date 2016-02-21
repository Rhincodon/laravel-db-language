<?php

namespace Rhinodontypicus\DBLanguage\Test;

use File;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Rhinodontypicus\DBLanguage\Models\Language;

abstract class TestCase extends Orchestra
{
    /**
     * @var
     */
    protected $languages;

    /**
     * Set Up
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Rhinodontypicus\DBLanguage\DbLanguageServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory($this->getTempDirectory());

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => "{$this->getTempDirectory()}/database.sqlite",
            'prefix'   => '',
        ]);

        $app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        file_put_contents("{$this->getTempDirectory()}/database.sqlite", null);

        include_once '__DIR__' . '/../resources/migrations/create_db_language_tables.php.stub';

        (new \CreateDbLanguageTables())->up();

        $this->languages = [
            Language::create(['name' => 'Russian']),
            Language::create(['name' => 'English']),
        ];
    }

    /**
     * @param $directory
     */
    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory);
    }

    /**
     * @param string $suffix
     * @return string
     */
    public function getTempDirectory($suffix = '')
    {
        return __DIR__ . '/temp' . ($suffix == '' ? '' : '/' . $suffix);
    }
}
