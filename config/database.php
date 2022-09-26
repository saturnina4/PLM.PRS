<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_OBJ,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env( 'DB_CONNECTION', 'mysql' ),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'prs' => [
            'driver'    => 'mysql',
            'host'      => env( 'PRS_DB_HOST', 'localhost' ),
            'port'      => env( 'PRS_DB_PORT', '3306' ),
            'database'  => env( 'PRS_DB_DATABASE', 'database' ),
            'username'  => env( 'PRS_DB_USERNAME', 'username' ),
            'password'  => env( 'PRS_DB_PASSWORD', 'password' ),
            'charset'   => env( 'PRS_DB_CHARSET', 'utf8mb4' ),
            'collation' => env( 'PRS_DB_COLLATION', 'utf8mb4_unicode_520_ci' ),
            'prefix'    => env( 'PRS_DB_PREFIX', '' ),
            'strict'    => env( 'PRS_DB_STRICT', true ),
            'engine'    => env( 'PRS_DB_ENGINE', 'InnoDB' ),
            'options'   => [
                PDO::MYSQL_ATTR_SSL_KEY  => 'C:/Certificates/MariaDB/ICTO-DBSRV100/ICTO-DBSRV100_Client.key',
                PDO::MYSQL_ATTR_SSL_CERT => 'C:/Certificates/MariaDB/ICTO-DBSRV100/ICTO-DBSRV100_Client.crt',
                PDO::MYSQL_ATTR_SSL_CA   => 'C:/Certificates/Root CA/Pamantasan ng Lungsod ng Maynila.crt',
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'default' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
