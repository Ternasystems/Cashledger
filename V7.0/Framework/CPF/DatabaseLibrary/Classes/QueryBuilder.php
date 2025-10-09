<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use PDO;
use PDOException;
use TS_Cache\Classes\CacheManager;
use TS_Configuration\Classes\AbstractCls;
use TS_Database\Enums\OrderByDirection;
use TS_Database\Enums\WhereType;
use TS_Exception\Classes\DBException;

/**
 * A fluent, secure query builder for all database interactions.
 */
class QueryBuilder extends AbstractCls
{
    private array $columns = ['*'];
    private array $wheres = [];
    private array $bindings = [];
    private ?string $orderBy = null;
    private ?string $limit = null;
    private ?int $cacheDuration = null; // In seconds

    public function __construct(private PDO $pdo, private string $table, public readonly ?string $modelClass = null, private ?CacheManager $cache = null)
    {
    }

    /**
     * Sets the cache duration for this query.
     *
     * @param int $seconds The number of seconds to cache the query result.
     * @return $this
     */
    public function cache(int $seconds = 60): self
    {
        $this->cacheDuration = $seconds;
        return $this;
    }

    public function select(string ...$columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, mixed $value, WhereType $type = WhereType::AND): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => $operator, 'value' => $value, 'type' => $type];
        return $this;
    }

    public function orderBy(string $column, OrderByDirection $direction = OrderByDirection::ASC): self
    {
        $this->orderBy = " ORDER BY `{$column}` {$direction->value}";
        return $this;
    }

    public function limit(?int $_limit, ?int $_offset): self
    {
        if (!is_null($_limit))
            $this->limit = " LIMIT {$_limit}";

        if (!is_null($_offset))
            $this->limit = ($this->limit ?? '') . " OFFSET {$_offset}";

        return $this;
    }

    /**
     * Executes the actual SELECT query against the database.
     */
    private function executeSelect(): array
    {
        $sql = "SELECT " . implode(', ', $this->columns) . " FROM {$this->table}";
        $sql .= $this->buildWhereClause();
        $sql .= $this->orderBy;
        $sql .= $this->limit;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->bindings);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new DBException('query_failed', [':reason' => $e->getMessage()], (int)$e->getCode(), $e);
        }
    }

    /**
     * Generates a unique cache key for the current query.
     */
    private function generateCacheKey(): string
    {
        $sql = "SELECT " . implode(', ', $this->columns) . " FROM {$this->table}";
        $sql .= $this->buildWhereClause();
        $sql .= $this->orderBy;
        $sql .= $this->limit;

        // Create a unique string from the SQL and its bindings.
        $keyData = $sql . json_encode($this->bindings);
        return 'query.' . md5($keyData);
    }

    /**
     * @throws DBException
     */
    public function get(): array
    {
        // If no cache duration is set, execute the query directly.
        if ($this->cacheDuration === null || $this->cache === null) {
            return $this->executeSelect();
        }

        $cacheKey = $this->generateCacheKey();

        // Check if the result is already in the cache.
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // If not, execute the query.
        $results = $this->executeSelect();

        // Store the fresh results in the cache.
        $this->cache->set($cacheKey, $results, $this->cacheDuration);

        return $results;
    }

    /**
     * @throws DBException
     */
    public function insert(array $data): int
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} (`{$columns}`) VALUES ({$placeholders})";

        return $this->execute($sql, array_values($data));
    }

    /**
     * @throws DBException
     */
    public function update(array $data): int
    {
        $setClauses = [];
        $params = [];
        foreach ($data as $column => $value) {
            $setClauses[] = "`{$column}` = ?";
            $params[] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses);
        $sql .= $this->buildWhereClause();

        return $this->execute($sql, array_merge($params, $this->bindings));
    }

    /**
     * @throws DBException
     */
    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->buildWhereClause();

        return $this->execute($sql, $this->bindings);
    }

    /**
     * @throws DBException
     */
    public function call(string $procedure, array $params = []): array
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "CALL {$procedure}({$placeholders})";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new DBException('procedure_call_failed', [':reason' => $e->getMessage()], (int)$e->getCode(), $e);
        }
    }

    private function buildWhereClause(): string
    {
        if (empty($this->wheres)) {
            $this->bindings = [];
            return '';
        }

        $sqlParts = [];
        $this->bindings = [];

        foreach ($this->wheres as $i => $where) {
            $prefix = ($i > 0) ? " {$where['type']->value} " : '';
            $operator = strtoupper($where['operator']);

            if (in_array($operator, ['IN', 'NOT IN']) && is_array($where['value'])) {
                if (empty($where['value'])) {
                    // Handle empty array case to avoid SQL errors
                    $sqlParts[] = $prefix . ($operator === 'IN' ? '0' : '1'); // WHERE 0 or WHERE 1
                    continue;
                }
                $placeholders = implode(', ', array_fill(0, count($where['value']), '?'));
                $sqlParts[] = $prefix . "`{$where['column']}` {$operator} ({$placeholders})";
                $this->bindings = array_merge($this->bindings, $where['value']);
            } else {
                $sqlParts[] = $prefix . "`{$where['column']}` {$where['operator']} ?";
                $this->bindings[] = $where['value'];
            }
        }
        return " WHERE " . ltrim(implode('', $sqlParts));
    }

    /**
     * @throws DBException
     */
    private function execute(string $sql, array $params): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new DBException('query_failed', [':reason' => $e->getMessage()], (int)$e->getCode(), $e);
        }
    }
}

