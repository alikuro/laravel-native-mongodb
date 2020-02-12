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

Register an alias for the MongoDB model by adding the following to the alias array in `config/app.php`:

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
