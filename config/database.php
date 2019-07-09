<?php

return [
    // required
    'database_type' => 'mysql',
    'database_name' => 'test',
    'server'        => 'percona',
    'username'      => 'root',
    'password'      => 'root',

    // [optional]
    'charset'       => 'utf8mb4',
    'collation'     => 'utf8mb4_general_ci',
    'port'          => 3306,

    // [optional] Table prefix
    'prefix'        => '',

    // [optional] Enable logging (Logging is disabled by default for better performance)
    'logging'       => true,

    // [optional] MySQL socket (shouldn't be used with server and port)
//    'socket'        => '',///tmp/mysql.sock

    // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
    'option'        => [
        \PDO::ATTR_CASE              => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS      => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::ATTR_EMULATE_PREPARES  => false,
    ],

    // [optional] Medoo will execute those commands after connected to the database for initialization //        'SET SQL_MODE=ANSI_QUOTES'
    'command'       => [

    ],
    'max_size'      => 2,//最大连接数
    'min_size'      => 1,//最小连接数
];