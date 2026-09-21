<?php
/**
 * This file is part of the ThunderPHP Framework.
 * It contains the QueryBuilder class which makes running queries easier.
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace Model;

use PDO;
use Closure;
use InvalidArgumentException;
use \Core\Database;

trait QueryBuilder
{
    
    protected string $table = '';
    protected ?int $lastInsertId = null;
    protected ?int $lastAffectedRows = null;

    protected array $selects = ['*'];
    protected array $joins = [];
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $orderBy = [];
    protected array $groupBy = [];
    protected array $havings = [];

    protected array $rawSelects = [];
    protected array $unions = [];

    protected ?int $limit = null;
    protected ?int $offset = null;
    protected ?int $page = null;
    protected ?int $perPage = null;

    protected ?string $lastError = null;
    protected ?int $lastErrorCode = null;
    protected ?string $lastSqlState = null;
 
    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function selectRaw(string $sql, array $bindings = []): self
    {
        $this->rawSelects[] = new RawExpression($sql, $bindings);
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    public function whereRaw(string $sql, array $bindings = [], string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => $sql,
            'bindings' => $bindings,
            'boolean' => $boolean
        ];
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function orWhereRaw(string $sql, array $bindings = []): self
    {
        return $this->whereRaw($sql, $bindings, 'OR');
    }

    /**
     * Raw ORDER BY
     */
    public function orderByRaw(string $sql, array $bindings = []): self
    {
        $this->orderBy[] = ['raw' => $sql];
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    /**
     * Execute a raw SQL query string directly.
     *
     * @param string $sql      SQL string with placeholders (named or positional)
     * @param array  $bindings Values to bind to the placeholders
     * @param bool   $fetchAll For SELECT queries, true = fetch all rows, false = fetch first row
     * @return object|int|false For SELECT: array of rows or single row; for INSERT/UPDATE/DELETE: number of affected rows; false on failure
     * @throws PDOException
     */
    public function raw(string $sql, array $bindings = [], bool $fetchAll = true): array|object|int|false
    {
        $this->clearError();
        try
        {
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($bindings);
            $this->reset();

            // Store affected rows for any query
            $this->lastAffectedRows = $stmt->rowCount();
            
            // For INSERT, lastInsertId is available
            $firstWord = strtoupper(strtok(trim($sql), ' '));
            if (in_array($firstWord, ['SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN', 'PRAGMA'])) {
                $this->lastInsertId = null; // No insert ID for SELECT
                return $fetchAll ? $stmt->fetchAll(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_OBJ);
            }
            
            // For INSERT, store lastInsertId
            if ($firstWord === 'INSERT') {
                $this->lastInsertId = (int) $this->connect()->lastInsertId();
            } else {
                $this->lastInsertId = null;
            }
            
            $this->reset();
            // For UPDATE, DELETE, etc. return affected rows
            return $this->lastAffectedRows;

        } catch (PDOException $e) {
            $this->storeError($e);
            $this->reset();
            throw $e;
        }
    }

    // -------------------- SELECT --------------------
    public function select(...$columns): self
    {
        $this->selects = $columns;
        return $this;
    }

    // -------------------- JOIN --------------------
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = [
            'type' => $type,
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    // -------------------- WHERE (with grouping) --------------------
    public function where(string $column, string $operator = null, $value = null, string $boolean = 'AND'): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];

        return $this;
    }

    public function orWhere(string $column, string $operator = null, $value = null): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function whereIn(string $column, array $values, string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean
        ];
        return $this;
    }

    public function whereNull(string $column, string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'type' => 'null',
            'column' => $column,
            'boolean' => $boolean
        ];
        return $this;
    }

    public function whereBetween(string $column, array $values, string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'type' => 'between',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean
        ];
        return $this;
    }

    // Nested conditions (e.g. ->where(function($q) { ... }))
    public function whereNested(Closure $callback, string $boolean = 'AND'): self
    {
        $nested = new static($this->connect(), $this->table);
        $callback($nested);
        $this->wheres[] = [
            'type' => 'nested',
            'query' => $nested,
            'boolean' => $boolean
        ];
        // Merge bindings from the nested builder
        $this->bindings = array_merge($this->bindings, $nested->getBindings());
        return $this;
    }

    // -------------------- ORDER & GROUP --------------------
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = compact('column', 'direction');
        return $this;
    }

    public function groupBy(...$columns): self
    {
        $this->groupBy = $columns;
        return $this;
    }

    public function having(string $column, string $operator, $value, string $boolean = 'AND'): self
    {
        $this->havings[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];
        return $this;
    }

    // -------------------- LIMIT & OFFSET --------------------
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    // -------------------- PAGINATION --------------------
    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $this->perPage = $perPage;
        $this->page = $page;
        $this->limit($perPage)->offset(($page - 1) * $perPage);

        $total = $this->clone()->count();
        $items = $this->get();

        $lastPage = (int) ceil($total / $perPage);

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $items ? ($page - 1) * $perPage + 1 : 0,
            'to' => $items ? min($total, $page * $perPage) : 0,
        ];
    }

    // -------------------- EXECUTION --------------------
    public function get(): array
    {
        $this->clearError();
        try{
            $sql = $this->toSelectSql();

            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($this->bindings);
            $this->reset();
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (PDOException $e) {
            $this->storeError($e);
            $this->reset();
            throw $e;
        }
    }

    public function first(): ?object
    {
        return $this->limit(1)->get()[0] ?? null;
    }

    public function count(string $column = '*'): int
    {
        $clone = $this->clone();
        $clone->selects = ["COUNT({$column}) as aggregate"];
        $result = $clone->first();
        return (int) ($result->aggregate ?? 0);
    }

    // INSERT
    public function insert(array $data): bool
    {
        $this->clearError();
        try
        {
            $columns = array_keys($data);
            $placeholders = array_map(fn($col) => ":$col", $columns);
            $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
            $stmt = $this->connect()->prepare($sql);

            $success = $stmt->execute($data);
            $this->reset();
            if ($success) {
                $this->lastInsertId = (int) $this->connect()->lastInsertId();
                $this->lastAffectedRows = $stmt->rowCount();
            }

            return $success;

        } catch (PDOException $e) {
            $this->storeError($e);
            throw $e;
        }
    }

    // UPDATE
    public function update(array $data): int
    {
        $this->clearError();
        try
        {

            $sets = [];
            foreach ($data as $col => $value) {
                $sets[] = "$col = :{$col}_update";
                $this->bindings["{$col}_update"] = $value;
            }
            $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . $this->compileWhere();
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($this->bindings);
            $this->reset();
            
            $this->lastAffectedRows = $stmt->rowCount();
            $this->lastInsertId = null; // No insert ID for update
            return $stmt->rowCount();

        } catch (PDOException $e) {
            $this->storeError($e);
            $this->reset();
            throw $e;
        }
    }

    // DELETE
    public function delete(): int
    {
        $this->clearError();
        try
        {
            $sql = "DELETE FROM {$this->table}" . $this->compileWhere();
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($this->bindings);
            $this->reset();

            $this->lastAffectedRows = $stmt->rowCount();
            $this->lastInsertId = null;

            return $stmt->rowCount();

        } catch (PDOException $e) {
            $this->storeError($e);
            throw $e;
        }
    }

    public function union(QueryBuilder|Closure $query, bool $all = false): self
    {
        if ($query instanceof Closure) {
            $builder = new static($this->connect(), '');
            $query($builder);
            $query = $builder;
        }
        
        $this->unions[] = [
            'query' => $query,
            'all' => $all
        ];
        
        // Merge bindings from the union query
        $this->bindings = array_merge($this->bindings, $query->getBindings());
        return $this;
    }

    public function unionAll(QueryBuilder|Closure $query): self
    {
        return $this->union($query, true);
    }

    /**
     * Insert or update multiple rows based on conflict columns.
     *
     * @param array $rows Array of associative arrays (column => value)
     * @param array $conflictColumns Columns that define uniqueness (e.g., ['id'] or ['email'])
     * @param array $updateColumns Columns to update on conflict (if empty, updates all except conflict columns)
     * @return int Number of affected rows
     */
    public function upsert(array $rows, array $conflictColumns, array $updateColumns = []): int
    {
        if (empty($rows)) {
            return 0;
        }
        
        $driver = $this->connect()->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        // Normalize rows (ensure all have same keys)
        $firstRow = reset($rows);
        $columns = array_keys($firstRow);
        
        // If updateColumns is empty, update all non-conflict columns
        if (empty($updateColumns)) {
            $updateColumns = array_diff($columns, $conflictColumns);
        }
        
        if ($driver === 'mysql') {
            return $this->mysqlUpsert($rows, $columns, $updateColumns);
        } elseif ($driver === 'pgsql') {
            return $this->pgsqlUpsert($rows, $conflictColumns, $columns, $updateColumns);
        } else {
            // Fallback: transaction with per-row merge (slow but safe)
            return $this->genericUpsert($rows, $conflictColumns, $columns, $updateColumns);
        }
    }

    protected function mysqlUpsert(array $rows, array $columns, array $updateColumns): int
    {
        $placeholders = [];
        $bindings = [];
        $valueIndex = 0;
        
        foreach ($rows as $row) {
            $rowPlaceholders = [];
            foreach ($columns as $col) {
                $param = ":u_{$valueIndex}";
                $rowPlaceholders[] = $param;
                $bindings[$param] = $row[$col];
                $valueIndex++;
            }
            $placeholders[] = "(" . implode(',', $rowPlaceholders) . ")";
        }
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES " 
               . implode(',', $placeholders)
               . " ON DUPLICATE KEY UPDATE ";
        
        $updates = [];
        foreach ($updateColumns as $col) {
            $updates[] = "$col = VALUES($col)";
        }
        $sql .= implode(',', $updates);
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($bindings);
        $this->reset();

        $this->lastAffectedRows = $stmt->rowCount();
        $this->lastInsertId = (int) $this->connect()->lastInsertId();

        return $stmt->rowCount();
    }

    protected function pgsqlUpsert(array $rows, array $conflictColumns, array $columns, array $updateColumns): int
    {
        $placeholders = [];
        $bindings = [];
        $valueIndex = 0;
        
        foreach ($rows as $row) {
            $rowPlaceholders = [];
            foreach ($columns as $col) {
                $param = ":u_{$valueIndex}";
                $rowPlaceholders[] = $param;
                $bindings[$param] = $row[$col];
                $valueIndex++;
            }
            $placeholders[] = "(" . implode(',', $rowPlaceholders) . ")";
        }
        
        $conflictList = implode(',', $conflictColumns);
        $updateList = [];
        foreach ($updateColumns as $col) {
            $updateList[] = "$col = EXCLUDED.$col";
        }
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES "
               . implode(',', $placeholders)
               . " ON CONFLICT ($conflictList) DO UPDATE SET "
               . implode(',', $updateList);
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($bindings);
        $this->reset();

        $this->lastAffectedRows = $stmt->rowCount();
        $this->lastInsertId = (int) $this->connect()->lastInsertId();

        return $stmt->rowCount();
    }

    protected function genericUpsert(array $rows, array $conflictColumns, array $columns, array $updateColumns): int
    {
        $this->clearError();
        // Simple fallback: delete conflicting rows and insert all (use transaction)
        $this->connect()->beginTransaction();
        try {
            $affected = 0;
            foreach ($rows as $row) {
                // Build WHERE condition from conflict columns
                $where = [];
                $bind = [];
                foreach ($conflictColumns as $col) {
                    $where[] = "$col = :conflict_$col";
                    $bind["conflict_$col"] = $row[$col];
                }
                $checkSql = "SELECT 1 FROM {$this->table} WHERE " . implode(' AND ', $where);
                $stmt = $this->connect()->prepare($checkSql);
                $stmt->execute($bind);
                $this->reset();
                $exists = $stmt->fetchColumn();
                
                if ($exists) {
                    // Update
                    $sets = [];
                    foreach ($updateColumns as $col) {
                        $sets[] = "$col = :update_$col";
                        $bind["update_$col"] = $row[$col];
                    }
                    $updateSql = "UPDATE {$this->table} SET " . implode(',', $sets) . " WHERE " . implode(' AND ', $where);
                    $stmt = $this->connect()->prepare($updateSql);
                    $stmt->execute($bind);
                    $this->reset();
                    $affected += $stmt->rowCount();
                } else {
                    // Insert
                    $cols = array_keys($row);
                    $placeholders = array_map(fn($c) => ":$c", $cols);
                    $insertSql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
                    $stmt = $this->connect()->prepare($insertSql);
                    $stmt->execute($row);
                    $this->reset();
                    $affected += $stmt->rowCount();
                }
            }
            $this->connect()->commit();
            $this->reset();
            return $affected;
        } catch (\Exception $e) {
            $this->connect()->rollBack();
            $this->reset();
            throw $e;
        }
    }

    // -------------------- SQL COMPILATION --------------------
    protected function toSelectSql(): string
    {
        $selectColumns = array_merge($this->selects, array_map(fn($raw) => $raw->sql, $this->rawSelects));
        $sql = "SELECT " . implode(', ', $selectColumns) . " FROM {$this->table}";

        $sql .= $this->compileJoins();
        $sql .= $this->compileWhere();
        $sql .= $this->compileGroupBy();
        $sql .= $this->compileHaving();
        $sql .= $this->compileOrderBy();
        $sql .= $this->compileLimitOffset();

        foreach ($this->unions as $union) {
            $sql .= ($union['all'] ? " UNION ALL " : " UNION ");
            $sql .= "(" . $union['query']->toSelectSql() . ")";
        }

        return $sql;
    }

    protected function compileJoins(): string
    {
        if (empty($this->joins)) return '';
        $sql = '';
        foreach ($this->joins as $join) {
            $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
        }
        return $sql;
    }

    protected function compileWhere(): string
    {
        if (empty($this->wheres)) return '';
        $sql = ' WHERE ';
        $first = true;
        foreach ($this->wheres as $where) {
            if (!$first) {
                $sql .= " {$where['boolean']} ";
            }
            $sql .= $this->compileWhereClause($where);
            $first = false;
        }
        return $sql;
    }

    protected function compileWhereClause(array $where): string
    {
        switch ($where['type']) {

            case 'basic':
                $param = $this->addBinding($where['value']);
                return "{$where['column']} {$where['operator']} {$param}";

            case 'in':
                $placeholders = [];
                foreach ($where['values'] as $val) {
                    $placeholders[] = $this->addBinding($val);
                }
                return "{$where['column']} IN (" . implode(',', $placeholders) . ")";

            case 'null':
                return "{$where['column']} IS NULL";

            case 'between':
                $p1 = $this->addBinding($where['values'][0]);
                $p2 = $this->addBinding($where['values'][1]);
                return "{$where['column']} BETWEEN {$p1} AND {$p2}";

            case 'nested':
                return '(' . $where['query']->toSelectSql() . ')';

            case 'raw':
                return '(' . $where['sql'] . ')';
                    
            default:
                throw new InvalidArgumentException("Unknown where type: {$where['type']}");
        }
    }

    protected function compileOrderBy(): string
    {
        if (empty($this->orderBy)) return '';
        $orders = array_map(fn($o) => "{$o['column']} {$o['direction']}", $this->orderBy);
        return " ORDER BY " . implode(', ', $orders);
    }

    protected function compileGroupBy(): string
    {
        return empty($this->groupBy) ? '' : " GROUP BY " . implode(', ', $this->groupBy);
    }

    protected function compileHaving(): string
    {
        if (empty($this->havings)) return '';
        $sql = ' HAVING ';
        $first = true;
        foreach ($this->havings as $having) {
            if (!$first) {
                $sql .= " {$having['boolean']} ";
            }
            $param = $this->addBinding($having['value']);
            $sql .= "{$having['column']} {$having['operator']} {$param}";
            $first = false;
        }
        return $sql;
    }

    protected function compileLimitOffset(): string
    {
        $sql = '';
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        return $sql;
    }

    // -------------------- BINDINGS & HELPERS --------------------
    protected function addBinding($value): string
    {
        $param = ':param_' . count($this->bindings);
        $this->bindings[$param] = $value;
        return $param;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Get the compiled SQL string without executing the query.
     * Useful for debugging and logging.
     * 
     * @return string
     */
    public function toSql(): string
    {
        $clone = clone $this;
        return $clone->toSelectSql();
    }

    /**
     * Get the current bindings array (values to be bound).
     * 
     * @return array
     */
    public function getRawBindings(): array
    {
        return $this->bindings;
    }

    protected function clone(): self
    {
        return clone $this;
    }

    public function reset(): self
    {
        $this->selects = ['*'];
        $this->joins = [];
        $this->wheres = [];
        $this->bindings = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->havings = [];
        $this->limit = null;
        $this->offset = null;
        $this->page = null;
        $this->perPage = null;
        return $this;
    }

    /**
     * Get the last insert ID from the last INSERT query.
     */
    public function getLastInsertId(): ?int
    {
        return $this->lastInsertId;
    }

    /**
     * Get the number of affected rows from the last INSERT, UPDATE, DELETE, or RAW query.
     */
    public function getAffectedRows(): ?int
    {
        return $this->lastAffectedRows;
    }

    protected function clearError(): void
    {
        $this->lastError = null;
        $this->lastErrorCode = null;
        $this->lastSqlState = null;
    }

    protected function storeError(PDOException $e): void
    {
        $this->lastError = $e->getMessage();
        $this->lastErrorCode = $e->getCode();
        $this->lastSqlState = $e->errorInfo[0] ?? null;
    }

    /**
     * Get the last error message (if any).
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Get the last error code (PDO exception code).
     */
    public function getLastErrorCode(): ?int
    {
        return $this->lastErrorCode;
    }

    /**
     * Get the last SQLSTATE error code (5 characters).
     */
    public function getLastSqlState(): ?string
    {
        return $this->lastSqlState;
    }


}