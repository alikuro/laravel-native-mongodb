Laravel Native MongoDB
======================

A Native mongo connection with PHP MongoDB Driver Manager.

Table of contents
-----------------
* [Installation](#installation)
* [Configuration](#configuration)
* [Query Builder](#query-builder)
* [Examples](#examples)

Installation
------------

Make sure you have the MongoDB PHP driver manager installed. You can find installation instructions at http://php.net/manual/en/mongodb.installation.php

**Only work** with newer version of mongodb PHP driver.

Installation using composer:

```
composer require alikuro/laravel-native-mongodb
```

### Laravel version Compatibility

 Laravel  |
:---------|
 4.2.x    |

Configuration
-------------

Adding the following config to the connections array for mongodb connection in `config/database.php`

```php
'mongodb' => [
    'driver' => 'mongodb',
    'host' => (preg_match("/,/",env('MONGO_HOST', '127.0.0.1'))? explode(',', env('MONGO_HOST', '127.0.0.1')) : env('MONGO_HOST', '127.0.0.1')),
    'port' => env('MONGO_PORT', '27017'),
    'database' => env('MONGO_DATABASE', 'default'),
    'username' => env('MONGO_USERNAME', 'default'),
    'password' => env('MONGO_PASSWORD', ''),
    'options' => (
        preg_match("/,/",env('MONGO_OPTIONS', null))?
            ( array_map(function($value){ $_tmp = []; for($i=0; $i<count($value); $i++){ if(is_array($value[$i])){ $_tmp+=$value[$i]; }else{ $_tmp+=[$value[$i]=>null]; } } return $_tmp; }, [ array_map(function($value){ $_tmp = array_map('trim', explode(':', $value)); return count($_tmp)>1?[$_tmp[0]=>$_tmp[1]]:trim($value); }, explode(',', env('MONGO_OPTIONS', null))) ])[0]):
            (count(explode(':', env('MONGO_OPTIONS', null)))>1?[explode(':', env('MONGO_OPTIONS', null))[0]=>explode(':', env('MONGO_OPTIONS', null))[1]]:env('MONGO_OPTIONS', null))
    ),
],
```

then you can put your config setup in `.env` file just like this:

```php
MONGO_HOST=127.0.0.1
MONGO_PORT=27017
MONGO_DATABASE=dev_table
MONGO_USERNAME=
MONGO_PASSWORD=
MONGO_OPTIONS=
```

Register the service provider for the MongoDB model by adding the following to the providers array in `config/app.php`:

```php
Alikuro\Mongodb\MongodbServiceProvider::class,
```

Register an alias for the MongoDB model by adding the following to the aliases array in `config/app.php`:

```php
'Xmongo'          => Alikuro\Mongodb\MongodbFacade::class,
```

This will allow you to use the following script :

```php
use Xmongo;
```
```php
private function foo(){
    . . . 
    
    Xmongo::db()-> . . .
    
    . . . 
}
```

Query Builder
-------------

. . . 

Examples
--------

. . . 
