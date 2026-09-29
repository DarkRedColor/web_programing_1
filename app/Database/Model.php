<?php
namespace App\Database;

use InvalidArgumentException;

/**
 * A small Eloquent-style base model.
 *
 * Each table gets a class that extends this and sets $table and $fillable.
 * One object = one row. Columns are read and written as properties:
 *
 *   $student = Student::find(3);
 *   echo $student->full_name;
 *   $student->course = 'Psychology';
 *   $student->save();
 */
abstract class Model
{
    /** The database table, e.g. 'students'. Set in the child class. */
    protected static string $table = '';

    /** Columns that may be set from user input (protects id, created_at, etc.). */
    protected static array $fillable = [];

    protected static string $primaryKey = 'id';

    /** The row's column values. */
    protected array $attributes = [];

    /** True once the row is in the database (decides INSERT vs UPDATE). */
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    // ---------- Starting queries ----------

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::class, static::$table);
    }

    /**
     * Lets you call query-builder methods straight on the model:
     * Student::where(...), Student::orderBy(...), Student::count(), ...
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return static::query()->$method(...$arguments);
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public static function find(int|string $id): ?static
    {
        return static::query()->where(static::$primaryKey, $id)->first();
    }

    /** Make a new row and save it in one step. */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /** Used by the query builder to turn a database row into an object. */
    public static function newFromRow(array $row): static
    {
        $model             = new static();
        $model->attributes = $row;   // straight from the DB, so fillable doesn't apply
        $model->exists     = true;
        return $model;
    }

    // ---------- Working with one row ----------

    /** Set several columns at once. Anything not in $fillable is ignored. */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, static::$fillable, true)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /** INSERT if new, UPDATE if it already exists. */
    public function save(): bool
    {
        $data = array_intersect_key($this->attributes, array_flip(static::$fillable));
        if (!$data) {
            throw new InvalidArgumentException('Nothing to save.');
        }

        $table   = '`' . static::$table . '`';
        $pk      = static::$primaryKey;
        $columns = array_keys($data);

        if ($this->exists) {
            $set  = implode(', ', array_map(fn ($c) => "`$c` = ?", $columns));
            $stmt = db()->prepare("UPDATE $table SET $set WHERE `$pk` = ?");
            return $stmt->execute([...array_values($data), $this->attributes[$pk]]);
        }

        $cols         = implode(', ', array_map(fn ($c) => "`$c`", $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $stmt         = db()->prepare("INSERT INTO $table ($cols) VALUES ($placeholders)");
        $stmt->execute(array_values($data));

        // Reload the row so id, created_at and other defaults are filled in.
        $fresh            = static::find(db()->lastInsertId());
        $this->attributes = $fresh ? $fresh->attributes : $this->attributes;
        $this->exists     = true;

        return true;
    }

    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        static::query()->where(static::$primaryKey, $this->attributes[static::$primaryKey])->delete();
        $this->exists = false;
        return true;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    // ---------- Property access: $student->full_name ----------

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}
