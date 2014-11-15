<?php

return array(

    'default' => 'mysql',
    'connections' => array(

        'mysql' => array(
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'eestec',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'unix_socket' => '/var/run/mysqld/mysqld.sock',
        ),

        'pgsql' => array(
            'driver' => 'pgsql',
            'host' => 'localhost',
            'database' => 'eestec',
            'username' => 'eestec',
            'password' => 'eestec',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        )

    ),
    'migrations' => 'migrations',

);
