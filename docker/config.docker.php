<?php
/**
 * Docker Configuration for Database Comparison Tool
 *
 * This configuration is automatically used when running with docker-compose
 */

return [
    'local' => [
        'host' => 'local-db',              // Docker service name
        'port' => '5432',
        'dbname' => 'platformdb',
        'user' => 'admin',
        'password' => 'admin_pass'
    ],
    'remote' => [
        'host' => 'remote-db',             // Docker service name
        'port' => '5432',                  // Internal port (not 5433)
        'dbname' => 'platformdb',
        'user' => 'postgres',
        'password' => 'remote_pass'
    ]
];
