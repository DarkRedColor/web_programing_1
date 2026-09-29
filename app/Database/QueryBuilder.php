<?php
namespace App\Database;

use InvalidArgumentException;
use PDOStatement;

/**
 * Builds a SELECT/DELETE query step by step, like Laravel's query builder:
 *
 *   Student::where('year_of_study', 2)->orderBy('full_name')->get();
 *
 * Values always go through prepared-statement placeholders (?),
 * and column names are checked, so user input can't inject SQL.
 */
class QueryBuilder
{
    private const OPERATORS = ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE'];

    private array $wheres   = [];   // e.g. [['AND', '`course` = ?'], ['OR', '`email` LIKE ?']]
    private array $bindings = [];   // the values for the ? placeholders, in order
    private array $orders   = [];
    private ?int  $limit    = null;

    public function __construct(
        private string $modelClass,  // e.g. App\Models\Student, used to turn rows into objects
        private string $table
    ) {}

    // ---------- Building the query ----------

    /** where('course', 'Psychology') or where('year_of_study', '>=', 2) */
    public function where(string $column, mixed $operator, mixed $value = null, string $boolean = 'AND'): static
    {
        // Two-argument form: where('course', 'Psychology') means "="
        if (func_num_args() === 2) {
            $value    = $operator;
            $operator = '=';
        }

        $operator = strtoupper($operator);
        if (!in_array($operator, self::OPERATORS, true)) {
            throw new InvalidArgumentException("Operator not allowed: $operator");
        }

        $this->wheres[]   = [$boolean, $this->column($column) . " $operator ?"];
        $this->bindings[] = $value;

        return $this;
    }

    public function orWhere(string $column, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value    = $operator;
            $operator = '=';
        }
        return $this->where($column, $operator, $value, 'OR');
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $direction      = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
        $this->orders[] = $this->column($column) . ' ' . $direction;
        return $this;
    }

    public function latest(string $column = 'id'): static
    {
        return $this->orderBy($column, 'desc');
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    // ---------- Running the query ----------

    /** All matching rows, as model objects. */
    public function get(): array
    {
        $sql = 'SELECT * FROM ' . $this->column($this->table) . $this->whereSql() . $this->orderSql();
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        $rows = $this->run($sql)->fetchAll();

        return array_map(fn (array $row) => $this->modelClass::newFromRow($row), $rows);
    }

    /** The first matching row, or null if there is none. */
    public function first(): ?Model
    {
        return $this->limit(1)->get()[0] ?? null;
    }

    public function exists(): bool
    {
        return $this->first() !== null;
    }

    public function count(): int
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->column($this->table) . $this->whereSql();
        return (int) $this->run($sql)->fetchColumn();
    }

    /** How many different values a column has, e.g. countDistinct('course'). */
    public function countDistinct(string $column): int
    {
        $sql = 'SELECT COUNT(DISTINCT ' . $this->column($column) . ') FROM ' . $this->column($this->table) . $this->whereSql();
        return (int) $this->run($sql)->fetchColumn();
    }

    /** Delete every matching row. Returns how many were deleted. */
    public function delete(): int
    {
        if (!$this->wheres) {
            throw new InvalidArgumentException('Refusing to delete without a where() condition.');
        }
        $sql = 'DELETE FROM ' . $this->column($this->table) . $this->whereSql();
        return $this->run($sql)->rowCount();
    }

    // ---------- Internals ----------

    private function whereSql(): string
    {
        if (!$this->wheres) {
            return '';
        }

        $sql = '';
        foreach ($this->wheres as $i => [$boolean, $condition]) {
            $sql .= ($i === 0 ? ' WHERE ' : " $boolean ") . $condition;
        }
        return $sql;
    }

    private function orderSql(): string
    {
        return $this->orders ? ' ORDER BY ' . implode(', ', $this->orders) : '';
    }

    private function run(string $sql): PDOStatement
    {
        $stmt = db()->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt;
    }

    /** Column names can't use placeholders, so only allow safe names and wrap them in backticks. */
    private function column(string $name): string
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
            throw new InvalidArgumentException("Invalid column name: $name");
        }
        return "`$name`";
    }
}
