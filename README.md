# Larvel Dynamic Config


## Introduction
This Package makes it possible for users to have their config files stored in a database table, making it easier to customize these values from UI.

## Installation
You can install the package via composer:
``` bash
composer require emadha/eloquent-views
```
The package will automatically register itself.

You can publish the config with:

Config file:
``` bash
php artisan vendor:publish --provider="EmadHa\EloquentViews\ServiceProvider" --tag="config"
```

Migration:
```bash
php artisan vendor:publish --provider="EmadHa\EloquentViews\ServiceProvider" --tag="migrations"
```
 
## Usage

First of all, you need to decide which config file(s) you want them to be stored in database by adding `'dynamic'=> true` to the file.

Example:
```php
# /config/app.php 
return [
    'dynamic' => true,
     ...
];
```
> Note that `dynamic` indicator is defined in `/config/emadha/dynamic-conf.php`:

The main config file of this packages is located in `/config/emadha/dynamic-conf.php` and contains the following code:
```php
<?php
return [
    /* The Config database table name */
    'table'                   => 'confs',

    /*
     * The key that defines which config file should be loaded dynamically
     * and store into the database
     * Add that key to any config file to make it dynamic.
     */
    'dynamic_key'             => 'dynamics',

    /*
     * they key which will have the defaults of a config key
     * example: config('defaults.app.name'); This is added on runtime.
     */
    'defaults_key'            => 'defaults',

    /*
     * Delete orphan keys
     * if set to true and delete a key from the actual config file,
     * that key will be deleted from database.
     */
    'auto_delete_orphan_keys' => true,
];
```

The content of that file is pretty clear to the user and well documented.

## Examples

```php
echo config('app.name'); // Will get the value from a config key
config('app.name')->setTo('Some New Value'); // will update the config key
config('app.name')->default(); // Will return the default value (from the actual config file and not from the database)
config('app.name')->revert(); // Will revert the value in database back to default 
```
As simple as that.

## Why is that Useful?
In case you ever wanted to control the site title from a UI (backend, frontend), in a dynamic way without having to edit the actual config file, then this a good approach.

Another Example: Let's say you have a google analytics code a long with some other customizations, and you have these in `/config/site.php` as follows:

```php
# /config/site.php
return [
    'dynamic'=>true,
    'title'=>config('app.name'),
    'description'=>'My Site Meta Description',
    'google'=>[
        'UA'=>'UA-XXXXXXXX-X',
        'enabled'=>true,
    ],
];
```
This config file cannot be easily modified from a user interface, thus your clients will not be able to edit this without editing the actual file, 
In that case this package will prove to be useful, adding the key `dynamic`=>true to that config file will make it store it's values in database using the same format as Laravel, therefore it will be no different for you to get the value of some key in that config file, example `config('site.google.UA)`, plus adding some nice features like updating the value and revert back to default.

With that approach you can now create a backend input to customize these, using one line of code `config('site.google.UA')->setTo('XYZ');` and then use it in your blade like normal:
```blade
{{-- welcome.blade.php--}}
<title>{{ config('site.title') }}</title>
<script>// Analytics ID: {{ config('site.google.UA')}}</script>
``` 