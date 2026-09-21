<?php

namespace Core;

use PDO;

class QueryBuilder {
    protected string $table = '';
    protected array $columns = ['*'];
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected ?string $modelClass = null;

    public function __construct(string $table, ?string $modelClass = null) {
        $this->table = $table;
        $this->modelClass = $modelClass;
    }

    public static function table(string $table): self {
        return new self($table);
    }

    public function select($columns = ['*']): self {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function where(string $column, $operator, $value = null): self {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
        ];
        $this->bindings[] = $value;

        return $this;
    }

    public function orWhere(string $column, $operator, $value = null): self {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator,
        ];
        $this->bindings[] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orders[] = "{$column} " . strtoupper($direction);
        return $this;
    }

    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }

    public function toSql(): string {
        $cols = implode(', ', $this->columns);
        $sql = "SELECT {$cols} FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereParts = [];
            foreach ($this->wheres as $index => $w) {
                $prefix = $index > 0 ? " {$w['type']} " : "";
                $whereParts[] = "{$prefix}{$w['column']} {$w['operator']} ?";
            }
            $sql .= implode('', $whereParts);
        }

        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    public function get(): array {
        $sql = $this->toSql();
        $stmt = Database::connect()->prepare($sql);
        $stmt->execute($this->bindings);
        $records = $stmt->fetchAll();

        if ($this->modelClass && class_exists($this->modelClass)) {
            return array_map(function ($row) {
                $model = new $this->modelClass();
                $model->setRawAttributes($row);
                $model->exists = true;
                return $model;
            }, $records);
        }

        return $records;
    }

    public function first() {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function find($id, string $primaryKey = 'id') {
        return $this->where($primaryKey, '=', $id)->first();
    }

    public function count(): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereParts = [];
            foreach ($this->wheres as $index => $w) {
                $prefix = $index > 0 ? " {$w['type']} " : "";
                $whereParts[] = "{$prefix}{$w['column']} {$w['operator']} ?";
            }
            $sql .= implode('', $whereParts);
        }

        $stmt = Database::connect()->prepare($sql);
        $stmt->execute($this->bindings);
        $result = $stmt->fetch();
        return (int)($result['count'] ?? 0);
    }

    public function insert(array $data) {
        $keys = array_keys($data);
        $columns = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = Database::connect()->prepare($sql);
        $stmt->execute(array_values($data));

        return Database::lastInsertId();
    }

    public function update(array $data): int {
        $setParts = [];
        $values = [];

        foreach ($data as $col => $val) {
            $setParts[] = "{$col} = ?";
            $values[] = $val;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereParts = [];
            foreach ($this->wheres as $index => $w) {
                $prefix = $index > 0 ? " {$w['type']} " : "";
                $whereParts[] = "{$prefix}{$w['column']} {$w['operator']} ?";
            }
            $sql .= implode('', $whereParts);
            $values = array_merge($values, $this->bindings);
        }

        $stmt = Database::connect()->prepare($sql);
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    public function delete(): int {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereParts = [];
            foreach ($this->wheres as $index => $w) {
                $prefix = $index > 0 ? " {$w['type']} " : "";
                $whereParts[] = "{$prefix}{$w['column']} {$w['operator']} ?";
            }
            $sql .= implode('', $whereParts);
        }

        $stmt = Database::connect()->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }

    public function paginate(int $perPage = 15, int $page = 1): array {
        $page = max(1, $page);
        $total = $this->count();
        $offset = ($page - 1) * $perPage;

        $items = $this->limit($perPage)->offset($offset)->get();

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage),
        ];
    }
}
