<?php
/**
 * Database Comparison Tool - Configuration Example
 *
 * Copy this file to config.php and update with your actual credentials.
 *
 * SECURITY WARNING:
 * - Never commit config.php to version control
 * - Use strong passwords in production
 * - Restrict database user permissions to read-only if possible
 */

return [
    /**
     * Local Database Configuration
     *
     * This is typically your development database
     */
    'local' => [
        'adapter' => 'PostgreSQLAdapter',  // Database adapter (PostgreSQLAdapter, MySQLAdapter, etc.)
        'host' => '127.0.0.1',             // Database host
        'port' => '5432',                   // Database port (PostgreSQL: 5432, MySQL: 3306)
        'dbname' => 'your_local_db',        // Database name
        'user' => 'your_username',          // Database user
        'password' => 'your_password'       // Database password
    ],

    /**
     * Remote Database Configuration
     *
     * This is typically your production or staging database
     * Can use a different adapter than local!
     */
    'remote' => [
        'adapter' => 'PostgreSQLAdapter',  // Database adapter
        'host' => 'remote.example.com',     // Remote database host
        'port' => '5432',                    // Database port
        'dbname' => 'your_remote_db',        // Database name
        'user' => 'your_username',           // Database user
        'password' => 'your_password'        // Database password
    ]
];

/**
 * ADAPTER EXAMPLES:
 *
 * PostgreSQL:
 * 'adapter' => 'PostgreSQLAdapter',
 * 'port' => '5432'
 *
 * MySQL/MariaDB (when available):
 * 'adapter' => 'MySQLAdapter',
 * 'port' => '3306'
 *
 * MSSQL (when available):
 * 'adapter' => 'MSSQLAdapter',
 * 'port' => '1433'
 */
