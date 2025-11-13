# Database Adapter Development Guide

This guide will help you create a new database adapter for the Database Comparison Tool.

## Table of Contents

- [Overview](#overview)
- [Adapter Architecture](#adapter-architecture)
- [Creating a New Adapter](#creating-a-new-adapter)
- [Testing Your Adapter](#testing-your-adapter)
- [Submitting Your Adapter](#submitting-your-adapter)

## Overview

The Database Comparison Tool uses an adapter pattern to support multiple database systems. Each database type (MySQL, MSSQL, Oracle, etc.) requires its own adapter that implements the `DatabaseAdapter` abstract class.

### Supported Adapters

- ✅ **PostgreSQLAdapter** - Fully implemented
- 🚧 **MySQLAdapter** - Skeleton available, needs implementation
- 📋 **MSSQLAdapter** - Planned
- 📋 **MariaDBAdapter** - Planned
- 📋 **OracleAdapter** - Planned
- 📋 **SQLiteAdapter** - Planned

## Adapter Architecture

### The DatabaseAdapter Abstract Class

All adapters must extend the `DatabaseAdapter` abstract class and implement the following methods:

```php
abstract class DatabaseAdapter
{
    // Required methods to implement
    abstract protected function connect();
    abstract public function getTablesAndColumns();
    abstract public function close();
    abstract public function getTypeName();
    abstract public function getVersion();
    abstract public function testConnection();

    // Optional (has default implementation)
    public function groupBySchema(array $data) { ... }
}
```

### Data Format Contract

The `getTablesAndColumns()` method MUST return data in this format:

```php
[
    'schema_name.table_name' => [
        ['name' => 'column1', 'type' => 'integer'],
        ['name' => 'column2', 'type' => 'varchar(50)'],
        ['name' => 'column3', 'type' => 'text']
    ],
    'public.users' => [
        ['name' => 'id', 'type' => 'integer'],
        ['name' => 'username', 'type' => 'varchar(50)'],
        ['name' => 'email', 'type' => 'varchar(100)']
    ]
]
```

**Key Points:**
- Keys: `"schema.table"` format
- Values: Array of columns
- Each column: `['name' => string, 'type' => string]`
- Column types should include precision (e.g., `varchar(50)`, `numeric(10,2)`)

## Creating a New Adapter

### Step 1: Create Adapter File

Create a new file in `adapters/` directory:

```bash
adapters/
├── DatabaseAdapter.php
├── PostgreSQLAdapter.php
├── MySQLAdapter.php          # Skeleton exists
└── YourNewAdapter.php         # Create this
```

### Step 2: Implement Required Methods

```php
<?php
require_once __DIR__ . '/DatabaseAdapter.php';

class MySQLAdapter extends DatabaseAdapter
{
    /**
     * Step 1: Connect to database
     */
    protected function connect()
    {
        // Use mysqli or PDO
        $this->conn = new mysqli(
            $this->config['host'],
            $this->config['user'],
            $this->config['password'],
            $this->config['dbname'],
            $this->config['port'] ?? 3306
        );

        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    /**
     * Step 2: Get tables and columns
     * THIS IS THE MOST IMPORTANT METHOD!
     */
    public function getTablesAndColumns()
    {
        $sql = "
            SELECT
                TABLE_SCHEMA,
                TABLE_NAME,
                COLUMN_NAME,
                COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
            ORDER BY TABLE_SCHEMA, TABLE_NAME, ORDINAL_POSITION
        ";

        $result = $this->conn->query($sql);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $key = $row['TABLE_SCHEMA'] . '.' . $row['TABLE_NAME'];
            $data[$key][] = [
                'name' => $row['COLUMN_NAME'],
                'type' => $row['COLUMN_TYPE']
            ];
        }

        return $data;
    }

    /**
     * Step 3: Close connection
     */
    public function close()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * Step 4: Return database type name
     */
    public function getTypeName()
    {
        return 'MySQL';
    }

    /**
     * Step 5: Get database version
     */
    public function getVersion()
    {
        $result = $this->conn->query('SELECT VERSION()');
        if ($result) {
            $row = $result->fetch_row();
            return $row[0];
        }
        return 'Unknown';
    }

    /**
     * Step 6: Test if connection is alive
     */
    public function testConnection()
    {
        return $this->conn && $this->conn->ping();
    }
}
```

### Step 3: Handle Database-Specific Column Types

Different databases represent column types differently. Make sure to:

1. Include precision/length (e.g., `VARCHAR(50)` not just `VARCHAR`)
2. Include scale for numeric types (e.g., `NUMERIC(10,2)`)
3. Use consistent naming

**Examples:**

```php
// PostgreSQL
'character varying(50)' => 'varchar(50)'
'numeric(10,2)' => 'numeric(10,2)'

// MySQL
'varchar(50)' => 'varchar(50)'  // Already in correct format
'int(11)' => 'int(11)'

// MSSQL
'nvarchar(50)' => 'nvarchar(50)'
'decimal(10,2)' => 'decimal(10,2)'
```

### Step 4: Configuration Example

Update `config.example.php` with your adapter:

```php
'local' => [
    'adapter' => 'MySQLAdapter',  // Your new adapter
    'host' => '127.0.0.1',
    'port' => '3306',             // Default MySQL port
    'dbname' => 'mydb',
    'user' => 'root',
    'password' => 'password'
]
```

## Testing Your Adapter

### Manual Testing

1. **Configure the database:**
   ```php
   // config.php
   'local' => [
       'adapter' => 'MySQLAdapter',
       'host' => 'localhost',
       'port' => '3306',
       // ...
   ]
   ```

2. **Test the connection:**
   ```php
   $adapter = new MySQLAdapter($config['local']);
   var_dump($adapter->testConnection()); // Should return true
   ```

3. **Test data retrieval:**
   ```php
   $data = $adapter->getTablesAndColumns();
   print_r($data); // Should return proper format
   ```

4. **Test in browser:**
   - Start the application
   - Open `http://localhost:8000`
   - Check if schemas appear correctly

### Checklist

- [ ] Connection works
- [ ] Returns data in correct format (`schema.table` keys)
- [ ] Column types include precision
- [ ] All schemas are detected (except system schemas)
- [ ] Column order is preserved
- [ ] Special characters in names work
- [ ] Large databases work (performance)
- [ ] Connection closes properly
- [ ] Error handling works

## Common Issues

### Issue 1: System Schemas Appearing

**Problem:** System schemas like `information_schema`, `pg_catalog`, `mysql` appear in results.

**Solution:** Exclude them in your SQL query:

```sql
WHERE table_schema NOT IN ('information_schema', 'pg_catalog', 'mysql', 'performance_schema', 'sys')
```

### Issue 2: Column Types Missing Precision

**Problem:** Types appear as `varchar` instead of `varchar(50)`.

**Solution:** Use database-specific type columns:

```sql
-- PostgreSQL
CASE WHEN character_maximum_length IS NOT NULL
     THEN 'varchar(' || character_maximum_length || ')'
     ELSE 'varchar'
END

-- MySQL (already includes precision)
COLUMN_TYPE  -- Returns 'varchar(50)'
```

### Issue 3: Connection Not Closing

**Problem:** Database connections remain open.

**Solution:** Implement `close()` properly:

```php
public function close()
{
    if ($this->conn) {
        // mysqli
        $this->conn->close();

        // PDO
        $this->conn = null;

        // pg
        pg_close($this->conn);
    }
}
```

## Database-Specific Notes

### MySQL/MariaDB

- Use `INFORMATION_SCHEMA.COLUMNS`
- `COLUMN_TYPE` already includes precision
- Exclude: `information_schema`, `mysql`, `performance_schema`, `sys`
- Default port: 3306

### Microsoft SQL Server

- Use `INFORMATION_SCHEMA.COLUMNS`
- Need to handle `nvarchar`, `nchar` types
- Exclude: `master`, `tempdb`, `model`, `msdb`
- Default port: 1433
- May need special connection string for Windows authentication

### Oracle

- Use `ALL_TAB_COLUMNS` or `USER_TAB_COLUMNS`
- Type is in `DATA_TYPE` + `DATA_LENGTH` / `DATA_PRECISION`
- Schema is in `OWNER` column
- May need special handling for NUMBER types

### SQLite

- Use `pragma_table_info()` for each table
- No schema concept (use 'main' as schema name)
- Type affinity is different from other databases

## Submitting Your Adapter

### Before Submitting

1. **Test thoroughly** - Test with real databases
2. **Add documentation** - Add adapter-specific notes
3. **Update README.md** - Add your adapter to supported list
4. **Follow code style** - Match existing code style
5. **Add examples** - Provide config examples

### Pull Request Checklist

- [ ] Adapter implements all required methods
- [ ] Adapter tested with real database
- [ ] Configuration example added
- [ ] README.md updated
- [ ] CHANGELOG.md updated
- [ ] Code follows PSR-12 standard
- [ ] No hardcoded values
- [ ] Error handling implemented
- [ ] Comments and documentation added

### PR Template

```markdown
## New Adapter: [Database Name]

### Description
Implements support for [Database Name] database system.

### Testing
- [x] Tested with [Database Name] version X.X
- [x] All tables and columns detected correctly
- [x] Column types include precision
- [x] Connection management works
- [x] Error handling works

### Configuration Example
\`\`\`php
'local' => [
    'adapter' => 'MySQLAdapter',
    'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'testdb',
    'user' => 'root',
    'password' => 'password'
]
\`\`\`

### Notes
- Special handling for [specific feature]
- Known limitation: [if any]
```

## Need Help?

- 📚 Check existing adapters: `PostgreSQLAdapter.php`
- 💬 Open a Discussion on GitHub
- 🐛 Report issues
- 📧 Contact maintainers

## Resources

- [PHP mysqli Documentation](https://www.php.net/manual/en/book.mysqli.php)
- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [INFORMATION_SCHEMA Documentation](https://dev.mysql.com/doc/refman/8.0/en/information-schema.html)

---

Thank you for contributing! 🎉
