<?php

namespace Core;

use JsonSerializable;

abstract class Model implements JsonSerializable {
    protected ?string $table = null;
    protected string $primaryKey = 'id';
    protected array $attributes = [];
    protected array $fillable = [];
    protected array $guarded = ['id'];
    public bool $exists = false;

    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }

    public function getTable(): string {
        if ($this->table) {
            return $this->table;
        }

        $className = (new \ReflectionClass($this))->getShortName();
        // Convert PascalCase to lowercase plural: User -> users, Post -> posts
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
    }

    public static function query(): QueryBuilder {
        $instance = new static();
        return new QueryBuilder($instance->getTable(), static::class);
    }

    public function fill(array $attributes): self {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public function setRawAttributes(array $attributes): void {
        $this->attributes = $attributes;
    }

    protected function isFillable(string $key): bool {
        if (in_array($key, $this->guarded)) {
            return false;
        }
        if (empty($this->fillable) || in_array($key, $this->fillable)) {
            return true;
        }
        return false;
    }

    public static function all(): array {
        return static::query()->get();
    }

    public static function find($id) {
        $instance = new static();
        return static::query()->find($id, $instance->primaryKey);
    }

    public static function where(string $column, $operator, $value = null): QueryBuilder {
        return static::query()->where($column, $operator, $value);
    }

    public static function create(array $attributes): self {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public function save(): bool {
        $table = $this->getTable();

        if ($this->exists) {
            $pk = $this->primaryKey;
            $id = $this->attributes[$pk] ?? null;
            if (!$id) {
                return false;
            }

            $updateData = $this->attributes;
            unset($updateData[$pk]);

            $qb = new QueryBuilder($table);
            $qb->where($pk, '=', $id)->update($updateData);
            return true;
        }

        $id = (new QueryBuilder($table))->insert($this->attributes);
        $this->attributes[$this->primaryKey] = $id;
        $this->exists = true;

        return true;
    }

    public function delete(): bool {
        if (!$this->exists) {
            return false;
        }

        $pk = $this->primaryKey;
        $id = $this->attributes[$pk] ?? null;
        if (!$id) {
            return false;
        }

        (new QueryBuilder($this->getTable()))->where($pk, '=', $id)->delete();
        $this->exists = false;
        return true;
    }

    public function __get(string $name) {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value) {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool {
        return isset($this->attributes[$name]);
    }

    public function toArray(): array {
        return $this->attributes;
    }

    public function jsonSerialize(): mixed {
        return $this->toArray();
    }
}
