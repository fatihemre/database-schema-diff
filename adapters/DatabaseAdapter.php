<?php
/**
 * Database Adapter Interface
 *
 * This abstract class defines the contract for database adapters.
 * Each database type (PostgreSQL, MySQL, MSSQL, etc.) should implement this class.
 *
 * @license GPL-3.0
 */

abstract class DatabaseAdapter
{
    protected $conn;
    protected $config;

    /**
     * Constructor
     *
     * @param array $config Database configuration
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Connect to database
     *
     * @return void
     * @throws Exception if connection fails
     */
    abstract protected function connect();

    /**
     * Get all tables and columns with their data types
     *
     * Returns an associative array where keys are "schema.table" and values are arrays of columns.
     * Each column is an array with 'name' and 'type' keys.
     *
     * Example return format:
     * [
     *     'public.users' => [
     *         ['name' => 'id', 'type' => 'integer'],
     *         ['name' => 'username', 'type' => 'varchar(50)'],
     *         ['name' => 'email', 'type' => 'varchar(100)']
     *     ],
     *     'public.posts' => [...]
     * ]
     *
     * @return array
     */
    abstract public function getTablesAndColumns();

    /**
     * Group tables by schema
     *
     * Converts flat "schema.table" keys into nested structure.
     * This method is usually the same for all adapters, but can be overridden if needed.
     *
     * @param array $data Data from getTablesAndColumns()
     * @return array Nested array grouped by schema
     */
    public function groupBySchema(array $data)
    {
        $schemas = [];
        foreach ($data as $key => $columns) {
            list($schema, $table) = explode('.', $key, 2);
            $schemas[$schema][$table] = $columns;
        }
        return $schemas;
    }

    /**
     * Close database connection
     *
     * @return void
     */
    abstract public function close();

    /**
     * Get database type name
     *
     * @return string (e.g., 'PostgreSQL', 'MySQL', 'MSSQL')
     */
    abstract public function getTypeName();

    /**
     * Get database version
     *
     * @return string
     */
    abstract public function getVersion();

    /**
     * Test connection
     *
     * @return bool
     */
    abstract public function testConnection();
}
