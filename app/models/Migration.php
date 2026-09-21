<?php

/**
 * This file is part of the ThunderPHP Framework.
 * Used by the thunder CLI tool.
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace Migration;
defined('ROOTPATH') or die("Direct script access denied");
use \Thunder\Database;

/**
 * Migration class to manage creating and dropping tables via the thunder CLI tool
 */
class Migration extends Database
{
    private array $columns      = [];
    private array $keys             = [];
    private array $data             = [];
    private array $primaryKeys  = [];
    private array $foreignKeys  = [];
    private array $uniqueKeys   = [];
    private array $fullTextKeys     = [];

    private string $charset     = 'utf8mb4';
    private string $collate     = 'utf8mb4_general_ci';
    private string $engine      = 'InnoDB';

    protected function resetBuilder(): void
    {
        $this->columns      = [];
        $this->keys         = [];
        $this->data         = [];
        $this->primaryKeys  = [];
        $this->foreignKeys  = [];
        $this->uniqueKeys   = [];
        $this->fullTextKeys = [];

        $this->charset  = 'utf8mb4';
        $this->collate  = 'utf8mb4_general_ci';
        $this->engine   = 'InnoDB';
    }

    /**
     * Create a new database table with defined columns, keys, and settings.
     * This is run once all the columns, keys & other settings have been set 
     * using other methods in the class.
     *
     * @param string $table Name of the table to create.
     * @return bool
     */
    public function createTable(string $table): bool
    {
        if(empty($this->columns)){
            echo "\n\rColumn data not found! Could not create table: $table";
            return false;
        }

        $parts = [];

        foreach ($this->columns as $column) {
            $parts[] = $column;
        }

        foreach ($this->primaryKeys as $name => $key) {
            $keyname = is_numeric($name) ? '' : "CONSTRAINT $name ";
            $keyvalues = is_array($key) ? implode(',', $key) : $key;
            $parts[] = "{$keyname}PRIMARY KEY ($keyvalues)";
        }

        foreach ($this->keys as $name => $key) {
            $keyname = is_numeric($name) ? '' : "$name ";
            $keyvalues = is_array($key) ? implode(',', $key) : $key;
            $parts[] = "KEY {$keyname}($keyvalues)";
        }

        foreach ($this->uniqueKeys as $name => $key) {
            $keyname = is_numeric($name) ? '' : "$name ";
            $keyvalues = is_array($key) ? implode(',', $key) : $key;
            $parts[] = "UNIQUE KEY {$keyname}($keyvalues)";
        }

        foreach ($this->fullTextKeys as $name => $key) {
            $keyname = is_numeric($name) ? '' : "$name ";
            $keyvalues = is_array($key) ? implode(',', $key) : $key;
            $parts[] = "FULLTEXT KEY {$keyname}($keyvalues)";
        }

        foreach ($this->foreignKeys as $fk) {
            $constraint = !empty($fk['name']) ? "CONSTRAINT {$fk['name']} " : '';
            $parts[] = $constraint
                . "FOREIGN KEY ({$fk['column']}) REFERENCES {$fk['ref_table']}({$fk['ref_column']})"
                . (!empty($fk['on_delete']) ? " ON DELETE {$fk['on_delete']}" : '')
                . (!empty($fk['on_update']) ? " ON UPDATE {$fk['on_update']}" : '');
        }

        $query = "CREATE TABLE IF NOT EXISTS $table (" . implode(',', $parts) . ")"
            . " ENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collate}";

        $this->query($query);
        if(!empty($this->error)){
            echo "\n\rError creating table $table with error: {$this->error}\n\nQuery: {$query}";
            return false;
        }

        $this->resetBuilder();
        echo "\n\rTable $table created successfully!";
        return true;
    }


    /**
     * Insert predefined data rows into the specified table.
     *
     * @param string $table Name of the table to insert data into.
     * @return bool
     */
    public function insert(string $table): bool
    {
        if(empty($this->data) || !is_array($this->data)){
            echo "\n\rRow data not found! No data inserted in table: $table";
            return false;
        }

        foreach ($this->data as $row) {
            $keys = array_keys($row);
            $columns_string = implode(",", $keys);
            $values_string = ':'.implode(",:", $keys);

            $query = "INSERT INTO $table ($columns_string) VALUES ($values_string)";
            $this->query($query,$row);

            if($this->has_error){
                echo "\n\rInsert failed in table $table with error: {$this->error}";
                return false;
            }
        }

        $this->data = [];
        echo "\n\rData inserted successfully in table: $table";
        return true;
    }


    /**
     * Add a column definition to the table creation queue.
     *
     * @param string $column SQL column definition string.
     * @return void
     */
    public function addColumn(string $column): void
    {
        $this->columns[] = $column;
    }


    /**
     * Add a regular key to the table creation queue.
     *
     * @param string|array $key Column name or array of column names.
     * @param string $name Optional index name.
     * @return void
     */
    public function addKey(string|array $key, string $name = ''): void
    {
        if($name !== ''){
            $this->keys[$name] = $key;
            return;
        }

        $this->keys[] = $key;
    }


    /**
     * Add a primary key to the table creation queue.
     *
     * @param string|array $primaryKey Column name or array of column names.
     * @param string $name Optional constraint name.
     * @return void
     */
    public function addPrimaryKey(string|array $primaryKey, string $name = ''): void
    {
        if($name !== ''){
            $this->primaryKeys[$name] = $primaryKey;
            return;
        }

        $this->primaryKeys[] = $primaryKey;
    }


    /**
     * Add a unique key to the table creation queue.
     *
     * @param string|array $key Column name or array of column names.
     * @param string $name Optional index name.
     * @return void
     */
    public function addUniqueKey(string|array $key, string $name = ''): void
    {
        if($name !== ''){
            $this->uniqueKeys[$name] = $key;
            return;
        }

        $this->uniqueKeys[] = $key;
    }


    /**
     * Add a fulltext key to the table creation queue.
     *
     * @param string|array $key Column name or array of column names.
     * @param string $name Optional index name.
     * @return void
     */
    public function addFullTextKey(string|array $key, string $name = ''): void
    {
        if($name !== ''){
            $this->fullTextKeys[$name] = $key;
            return;
        }

        $this->fullTextKeys[] = $key;
    }

    /**
     * Add a foreign key to the table creation queue.
     */
    public function addForeignKey(
        string $column,
        string $refTable,
        string $refColumn = 'id',
        string $onDelete = 'CASCADE',
        string $onUpdate = 'CASCADE',
        string $name = ''
    ): void
    {
        $this->foreignKeys[] = [
            'name' => $name,
            'column' => $column,
            'ref_table' => $refTable,
            'ref_column' => $refColumn,
            'on_delete' => strtoupper($onDelete),
            'on_update' => strtoupper($onUpdate),
        ];
    }

    /**
     * Set the table collation for creation.
     */
    public function addCollate(string $value): void
    {
        $this->collate = $value;
    }

    /**
     * Set the table storage engine.
     */
    public function addEngine(string $value): void
    {
        $this->engine = $value;
    }

    /**
     * Set the table character set.
     */
    public function addCharset(string $value): void
    {
        $this->charset = $value;
    }

    /**
     * Add a data row for insertion.
     */
    public function addData(array $data): void
    {
        $this->data[] = $data;
    }


    /**
     * Drop a database table if it exists.
     */
    public function dropTable(string $table): bool
    {
        $query = "DROP TABLE IF EXISTS $table";
        $this->query($query);

        if($this->has_error){
            echo "\n\rError deleting table $table: {$this->error}";
            return false;
        }

        echo "\n\rTable $table deleted successfully!";
        return true;
    }

    public function addColumnToTable(string $table, string $columnSql): bool
    {
        $query = "ALTER TABLE $table ADD COLUMN $columnSql";
        $this->query($query);
        return !$this->has_error;
    }

    public function modifyColumn(string $table, string $columnSql): bool
    {
        $query = "ALTER TABLE $table MODIFY COLUMN $columnSql";
        $this->query($query);
        return !$this->has_error;
    }

    public function renameColumn(string $table, string $oldName, string $newName, string $newDefinition): bool
    {
        $query = "ALTER TABLE $table CHANGE COLUMN $oldName $newName $newDefinition";
        $this->query($query);
        return !$this->has_error;
    }

    public function dropColumn(string $table, string $column): bool
    {
        $query = "ALTER TABLE $table DROP COLUMN $column";
        $this->query($query);
        return !$this->has_error;
    }

    public function addForeignKeyToTable(
        string $table,
        string $column,
        string $refTable,
        string $refColumn = 'id',
        string $onDelete = 'CASCADE',
        string $onUpdate = 'CASCADE',
        string $name = ''
    ): bool
    {
        $constraint = $name ?: 'fk_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $table . '_' . $column);

        $query = "ALTER TABLE $table ADD CONSTRAINT $constraint FOREIGN KEY ($column) REFERENCES $refTable($refColumn)"
            . " ON DELETE " . strtoupper($onDelete)
            . " ON UPDATE " . strtoupper($onUpdate);

        $this->query($query);
        return !$this->has_error;
    }

    public function dropForeignKey(string $table, string $constraintName): bool
    {
        $query = "ALTER TABLE $table DROP FOREIGN KEY $constraintName";
        $this->query($query);
        return !$this->has_error;
    }

    public function addIndexToTable(string $table, string|array $columns, string $name = ''): bool
    {
        $name = $name ?: 'idx_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns));
        $cols = is_array($columns) ? implode(',', $columns) : $columns;
        $query = "ALTER TABLE $table ADD INDEX $name ($cols)";
        $this->query($query);
        return !$this->has_error;
    }

    public function addUniqueIndexToTable(string $table, string|array $columns, string $name = ''): bool
    {
        $name = $name ?: 'uniq_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns));
        $cols = is_array($columns) ? implode(',', $columns) : $columns;
        $query = "ALTER TABLE $table ADD UNIQUE KEY $name ($cols)";
        $this->query($query);
        return !$this->has_error;
    }

    public function dropIndex(string $table, string $name): bool
    {
        $query = "ALTER TABLE $table DROP INDEX $name";
        $this->query($query);
        return !$this->has_error;
    }
}
