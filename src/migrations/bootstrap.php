<?php
require './vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

// Create a new Capsule instance
$capsule = new Capsule;

// Add a connection
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'db', // Use the service name defined in docker-compose
    'database' => 'youngdev',
    'username' => 'user',
    'password' => 'root',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

// Set the event dispatcher used by Eloquent models
$capsule->setEventDispatcher(new Dispatcher());

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Boot Eloquent
$capsule->bootEloquent();
