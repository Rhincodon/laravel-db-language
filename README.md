# Laravel DB Language

[![Latest Version](https://img.shields.io/github/release/Rhincodon/laravel-db-language.svg?style=flat-square)](https://github.com/Rhincodon/laravel-db-language/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Rhincodon/laravel-db-language/master.svg?style=flat-square)](https://travis-ci.org/Rhincodon/laravel-db-language)
[![Total Downloads](https://img.shields.io/packagist/dt/Rhincodon/laravel-db-language.svg?style=flat-square)](https://packagist.org/packages/Rhincodon/laravel-db-language)

## Install

Via Composer

``` bash
$ composer require rhincodon/laravel-db-language
```

## Usage

Register Service Provider in `config/app.php`:

```php
Rhinodontypicus\DBLanguage\DbLanguageServiceProvider::class,
```

Publish config and migration and migrate:

```bash
php artisan vendor:publish
php artisan migrate
```

Package have 3 models — Language, Value, Constant. You can extend them and use in your app to create language/value/constant.

For usage you need to create one Language in your database. Then you can load all constants/values in your middleware for language and use them like so:

```php
$language = \Rhinodontypicus\DBLanguage\Language::create(['name' => 'English']); // Create language

// Somewhere in your middleware you can load all constants
db_language()->load($language->id); // Load all constants for language
db_language()->load($language->id, 'site'); // Load all constants for language from 'site' group

// Somewhere in view/controller, group::value syntax
db_language('site::some_constant'); // Get language constant for loaded language
db_language('site::some_constant', 'Default Value'); // Get language constant with default value. If constant does not exists, value will be created in database for the first time

db_language()->language(); // Get current loaded language
db_language()->language('name'); // Current loaded language field
```

## Credits

- [rhinodontypicus](https://github.com/rhincodon)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.