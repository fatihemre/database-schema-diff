<?php
/**
 * PostgreSQL Database Adapter
 *
 * @license GPL-3.0
 */

require_once __DIR__ . '/DatabaseAdapter.php';

class PostgreSQLAdapter extends DatabaseAdapter
{
    /**
     * Connect to PostgreSQL database
     *
     * @return void
     * @throws Exception if connection fails
     */
    protected function connect()
    {
        $connStr = sprintf(
            "host=%s port=%s dbname=%s user=%s password=%s",
            $this->config['host'],
            $this->config['port'],
            $this->config['dbname'],
            $this->config['user'],
            $this->config['password']
        );

        $this->conn = pg_connect($connStr);

        if (!$this->conn) {
            throw new Exception("PostgreSQL connection failed: " . pg_last_error());
        }
    }

    /**
     * Get all tables and columns with their data types
     *
     * @return array
     */
    public function getTablesAndColumns()
    {
        $sql = "
            SELECT
                table_schema,
                table_name,
                column_name,
                CASE
                    WHEN data_type = 'character varying' THEN
                        CASE WHEN character_maximum_length IS NOT NULL
                             THEN 'varchar(' || character_maximum_length || ')'
                             ELSE 'varchar'
                        END
                    WHEN data_type = 'character' THEN
                        CASE WHEN character_maximum_length IS NOT NULL
                             THEN 'char(' || character_maximum_length || ')'
                             ELSE 'char'
                        END
                    WHEN data_type = 'numeric' THEN
                        CASE WHEN numeric_precision IS NOT NULL AND numeric_scale IS NOT NULL
                             THEN 'numeric(' || numeric_precision || ',' || numeric_scale || ')'
                             WHEN numeric_precision IS NOT NULL
                             THEN 'numeric(' || numeric_precision || ')'
                             ELSE 'numeric'
                        END
                    ELSE data_type
                END as full_type
            FROM information_schema.columns
            WHERE table_schema NOT IN ('pg_catalog', 'information_schema')
            ORDER BY table_schema, table_name, ordinal_position;
        ";

        $result = pg_query($this->conn, $sql);

        if (!$result) {
            throw new Exception("Query failed: " . pg_last_error($this->conn));
        }

        $data = [];

        while ($row = pg_fetch_assoc($result)) {
            $key = $row['table_schema'] . '.' . $row['table_name'];
            $data[$key][] = [
                'name' => $row['column_name'],
                'type' => $row['full_type']
            ];
        }

        return $data;
    }

    /**
     * Close PostgreSQL connection
     *
     * @return void
     */
    public function close()
    {
        if ($this->conn) {
            pg_close($this->conn);
        }
    }

    /**
     * Get database type name
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'PostgreSQL';
    }

    /**
     * Get PostgreSQL version
     *
     * @return string
     */
    public function getVersion()
    {
        $result = pg_query($this->conn, 'SELECT version()');
        if ($result) {
            $row = pg_fetch_row($result);
            return $row[0];
        }
        return 'Unknown';
    }

    /**
     * Test connection
     *
     * @return bool
     */
    public function testConnection()
    {
        if (!$this->conn) {
            return false;
        }

        // PHP 8.1+ returns PgSql\Connection object, older versions return resource
        $isValid = (is_resource($this->conn) || is_object($this->conn));

        if (!$isValid) {
            return false;
        }

        return pg_connection_status($this->conn) === PGSQL_CONNECTION_OK;
    }
}
