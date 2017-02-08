# Laravel Hypernova

Laravel Hypernova is a package to use the Hypernova server-side rendering service from Airbnb (https://github.com/airbnb/hypernova). It enables you to render Javascript UI views on the server and use it in your Laravel views. The advantage of this technique is that the response will contain the actual HTML of your Javascript views and helps on things like SEO.

[![Latest Stable Version](https://poser.pugx.org/folklore/laravel-hypernova/v/stable.svg)](https://packagist.org/packages/folklore/laravel-hypernova)
[![Build Status](https://travis-ci.org/Folkloreatelier/laravel-hypernova.png?branch=master)](https://travis-ci.org/Folkloreatelier/laravel-hypernova)
[![Coverage Status](https://coveralls.io/repos/Folkloreatelier/laravel-hypernova/badge.svg?branch=master&service=github)](https://coveralls.io/github/Folkloreatelier/laravel-hypernova?branch=master)
[![Total Downloads](https://poser.pugx.org/folklore/laravel-hypernova/downloads.svg)](https://packagist.org/packages/folklore/laravel-hypernova)


## Installation

#### Dependencies:

* [Laravel 5.x](https://github.com/laravel/laravel)
* [hypernova-php 1.0](https://github.com/wayfair/hypernova-php)

#### Installation:

**1-** Require the package via Composer in your `composer.json`.
```json
{
	"require": {
		"folklore/laravel-hypernova": "~0.1.0"
	}
}
```

**2-** Run Composer to install or update the new requirement.

```bash
$ composer install
```

or

```bash
$ composer update
```

**3-** Add the service provider to your `app/config/app.php` file
```php
'Folklore\Hypernova\HypernovaServiceProvider',
```

**4-** Add the facade (optional) to your `app/config/app.php` file
```php
'Hypernova' => 'Folklore\Hypernova\Facades\Hypernova',
```

**5-** Publish the configuration file and public files

```bash
$ php artisan vendor:publish --provider="Folklore\Hypernova\HypernovaServiceProvider"
```

**6-** Review the configuration file

```
app/config/hypernova.php
```
