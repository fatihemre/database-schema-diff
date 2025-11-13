<?php
/**
 * Database Comparison API with Adapter Support
 *
 * @license GPL-3.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

try {
    $config = require 'config.php';

    // Load adapter classes
    $adapterPath = __DIR__ . '/adapters/';
    require_once $adapterPath . 'DatabaseAdapter.php';

    // Get adapter class names
    $localAdapterClass = $config['local']['adapter'] ?? 'PostgreSQLAdapter';
    $remoteAdapterClass = $config['remote']['adapter'] ?? 'PostgreSQLAdapter';

    // Load and instantiate adapters
    require_once $adapterPath . $localAdapterClass . '.php';
    require_once $adapterPath . $remoteAdapterClass . '.php';

    if (!class_exists($localAdapterClass)) {
        throw new Exception("Local adapter class '{$localAdapterClass}' not found");
    }

    if (!class_exists($remoteAdapterClass)) {
        throw new Exception("Remote adapter class '{$remoteAdapterClass}' not found");
    }

    // Create adapter instances
    try {
        $localAdapter = new $localAdapterClass($config['local']);
    } catch (Exception $e) {
        throw new Exception("Local database connection failed: " . $e->getMessage());
    }

    try {
        $remoteAdapter = new $remoteAdapterClass($config['remote']);
    } catch (Exception $e) {
        throw new Exception("Remote database connection failed: " . $e->getMessage());
    }

    // Test connections
    if (!$localAdapter->testConnection()) {
        $localInfo = "Host: {$config['local']['host']}:{$config['local']['port']}, DB: {$config['local']['dbname']}";
        throw new Exception("Local database connection test failed. $localInfo. Please check your credentials and ensure PostgreSQL is running.");
    }

    if (!$remoteAdapter->testConnection()) {
        $remoteInfo = "Host: {$config['remote']['host']}:{$config['remote']['port']}, DB: {$config['remote']['dbname']}";
        throw new Exception("Remote database connection test failed. $remoteInfo. Please check your credentials and ensure PostgreSQL is running.");
    }

    // Get data from both databases
    $localData = $localAdapter->getTablesAndColumns();
    $remoteData = $remoteAdapter->getTablesAndColumns();

    // Group by schema
    $localSchemas = $localAdapter->groupBySchema($localData);
    $remoteSchemas = $remoteAdapter->groupBySchema($remoteData);

    // Check schema statuses
    $schemaStatuses = [];
    $allSchemas = array_unique(array_merge(
        array_keys($localSchemas),
        array_keys($remoteSchemas)
    ));

    foreach ($allSchemas as $schema) {
        $schemaStatuses[$schema] = checkSchemaStatus(
            $schema,
            $localSchemas,
            $remoteSchemas
        );
    }

    // Get metadata before closing connections
    $localVersion = $localAdapter->getVersion();
    $remoteVersion = $remoteAdapter->getVersion();
    $localTypeName = $localAdapter->getTypeName();
    $remoteTypeName = $remoteAdapter->getTypeName();

    // Close connections
    $localAdapter->close();
    $remoteAdapter->close();

    // Response
    $response = [
        'success' => true,
        'data' => [
            'local' => $localSchemas,
            'remote' => $remoteSchemas,
            'schemaStatuses' => $schemaStatuses,
            'config' => [
                'local' => [
                    'host' => $config['local']['host'],
                    'port' => $config['local']['port'],
                    'adapter' => $localTypeName
                ],
                'remote' => [
                    'host' => $config['remote']['host'],
                    'port' => $config['remote']['port'],
                    'adapter' => $remoteTypeName
                ]
            ],
            'meta' => [
                'local_version' => $localVersion,
                'remote_version' => $remoteVersion
            ]
        ]
    ];

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Check if schema is identical between local and remote
 *
 * @param string $schema Schema name
 * @param array $localSchemas Local schemas data
 * @param array $remoteSchemas Remote schemas data
 * @return bool
 */
function checkSchemaStatus($schema, $localSchemas, $remoteSchemas)
{
    $localTables = isset($localSchemas[$schema]) ? $localSchemas[$schema] : [];
    $remoteTables = isset($remoteSchemas[$schema]) ? $remoteSchemas[$schema] : [];

    // Different table count
    if (count($localTables) !== count($remoteTables)) {
        return false;
    }

    // Check each table
    foreach ($localTables as $table => $columns) {
        if (!isset($remoteTables[$table])) {
            return false;
        }

        // Different column count
        if (count($columns) !== count($remoteTables[$table])) {
            return false;
        }

        // Compare columns (name and type)
        foreach ($columns as $index => $column) {
            $remoteColumn = isset($remoteTables[$table][$index])
                ? $remoteTables[$table][$index]
                : null;

            if (!$remoteColumn ||
                $column['name'] !== $remoteColumn['name'] ||
                $column['type'] !== $remoteColumn['type']) {
                return false;
            }
        }
    }

    return true;
}
