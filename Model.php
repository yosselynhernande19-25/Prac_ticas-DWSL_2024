<?php

require_once 'Database.php';
require_once 'Paginator.php';

class Model
{
    protected $table;
    protected $tableAlias;
    protected $primaryKey = 'id';
    protected $fillable = [];

    protected $attributes = [];
    protected $wheres = [];
    protected $joins = [];
    protected $aggregates = [];
    protected $groupBy = [];
    protected $orderBy = [];
    protected $select = '*';
    protected $selectAlias = '';
    protected $take;

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getTableAlias()
    {
        return $this->tableAlias;
    }

    public static function all()
    {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table}";
        $stmt = $instance->getConnection()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    public static function find($id)
    {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = :id";
        $stmt = $instance->getConnection()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetch(PDO::FETCH_CLASS);
    }

    public function save()
    {
        if (empty($this->attributes) || empty($this->fillable)) {
            return false;
        }
    
        $fillableAttributes = array_intersect_key($this->attributes, array_flip($this->fillable));
    
        if (empty($fillableAttributes)) {
            return false;
        }
    
        $columns = implode(',', array_keys($fillableAttributes));
        $values = ':' . implode(', :', array_keys($fillableAttributes));
    
        $query = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $stmt = $this->getConnection()->prepare($query);
    
        if (!$stmt) {
            return false;
        }

        foreach ($fillableAttributes as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
    
        return $stmt->execute();
    }

    public function update($id)
    {
        if (empty($this->attributes) || empty($this->fillable)) {
            return false;
        }

        $fillableAttributes = array_intersect_key($this->attributes, array_flip($this->fillable));

        if (empty($fillableAttributes)) {
            return false;
        }

        $updateData = [];
        foreach ($fillableAttributes as $key => $value) {
            $updateData[$key] = $value;
        }

        $query = "UPDATE {$this->table} SET ";
        foreach ($updateData as $key => $value) {
            $query .= "$key=:$key, ";
        }

        $query = rtrim($query, ', ');

        $query .= " WHERE {$this->primaryKey} = :id";

        $stmt = $this->getConnection()->prepare($query);

        if (!$stmt) {
            return false;
        }

        foreach ($updateData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->getConnection()->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function table($tableName, $alias = null)
    {
        $this->table = $tableName;
        $this->tableAlias = $alias;
        return $this;
    }

    public function select(...$columns)
    {
        $this->select = implode(', ', $columns);
        return $this;
    }

    public function selectAlias($alias)
    {
        $this->selectAlias = $alias;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->wheres[] = compact('column', 'operator', 'value');
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $this->wheres[] = ['column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => 'OR'];
        return $this;
    }

    public function whereBetween($column, $values)
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'BETWEEN', 'value' => $values, 'boolean' => 'AND'];
        return $this;
    }

    public function whereIn($column, $values)
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IN', 'value' => $values, 'boolean' => 'AND'];
        return $this;
    }

    public function join($table, $firstColumn, $operator, $secondColumn)
    {
        $this->joins[] = compact('table', 'firstColumn', 'operator', 'secondColumn');
        return $this;
    }

    public function groupBy(...$columns)
    {
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->orderBy = compact('column', 'direction');
        return $this;
    }


    public function count($column = '*')
    {
        $this->aggregates[] = "COUNT($column) as count";
        return $this;
    }

    public function sum($column)
    {
        $this->aggregates[] = "SUM($column) as sum";
        return $this;
    }

    public function avg($column)
    {
        $this->aggregates[] = "AVG($column) as avg";
        return $this;
    }

    public function max($column)
    {
        $this->aggregates[] = "MAX($column) as max";
        return $this;
    }

    public function min($column)
    {
        $this->aggregates[] = "MIN($column) as min";
        return $this;
    }

    public function take($count)
    {
        $this->take = $count;
        return $this;
    }

    public function get()
    {
        $query = $this->buildQuery();
        $stmt = $this->getConnection()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    public function first()
    {
        $query = $this->buildQuery() . ' LIMIT 1';
        $stmt = $this->getConnection()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetch(PDO::FETCH_CLASS);
    }

    public function paginate($perPage = 10, $page = 1)
    {
        $offset = ($page - 1) * $perPage;

        $countQuery = $this->buildCountQuery();
        $countStmt = $this->getConnection()->query($countQuery);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];

        $query = $this->buildQuery() . " LIMIT $perPage OFFSET $offset";
        $stmt = $this->getConnection()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $items = $stmt->fetchAll(PDO::FETCH_CLASS);

        return new Paginator($items, $perPage, $page, $totalCount);
    }

    private function buildQuery()
    {
        $columns = $this->select ? $this->select : '*';
    
        if (!empty($this->wheres)) {
            $whereClause = $this->buildWhereClause($this->wheres);
        } else {
            $whereClause = '';
        }
    
        $joins = $this->buildJoinClause($this->joins);
        $aggregates = $this->aggregates ? implode(', ', $this->aggregates) : '';
        $groupBy = !empty($this->groupBy) ? 'GROUP BY ' . implode(', ', $this->groupBy) : '';
        $limit = !empty($this->take) ? 'LIMIT ' . $this->take : '';
        $orderBy = !empty($this->orderBy) ? 'ORDER BY ' . $this->orderBy['column'] . ' ' . strtoupper($this->orderBy['direction']) : '';
    
        $select = $this->select;
        if ($this->selectAlias) {
            $select .= " as {$this->selectAlias}";
        }
    
        $table = $this->table;
        if ($this->tableAlias) {
            $table .= " as {$this->tableAlias}";
        }
    
        return "SELECT $select $aggregates FROM $table $joins $whereClause $groupBy $orderBy $limit";
    }

    private function buildCountQuery()
    {
        $joins = $this->buildJoinClause($this->joins);

        $whereClause = !empty($this->wheres) ? $this->buildWhereClause($this->wheres) : '';
        $groupBy = !empty($this->groupBy) ? 'GROUP BY ' . implode(', ', $this->groupBy) : '';

        $table = $this->table;
        if ($this->tableAlias) {
            $table .= " as {$this->tableAlias}";
        }

        return "SELECT COUNT(*) as count FROM $table $joins $whereClause $groupBy";
    }


    protected function buildWhereClause($wheres)
    {
        $where = 'WHERE ';
        foreach ($wheres as $condition) {
            $where .= "{$condition['column']} {$condition['operator']} '{$condition['value']}' AND ";
        }

        $where = rtrim($where, ' AND ');

        return $where;
    }

    protected function buildJoinClause($joins)
    {
        $join = '';
        foreach ($joins as $joinClause) {
            $join .= "JOIN {$joinClause['table']} ON {$joinClause['firstColumn']} {$joinClause['operator']} {$joinClause['secondColumn']} ";
        }

        return $join;
    }

    public function hasMany($relatedModel, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->table . '_id';
        $localKey = $localKey ?: $this->primaryKey;

        return (new $relatedModel())->where($foreignKey, '=', $this->$localKey);
    }

    public function belongsTo($relatedModel, $foreignKey = null, $otherKey = null)
    {
        $foreignKey = $foreignKey ?: $relatedModel::getTable() . '_id';
        $otherKey = $otherKey ?: 'id';

        return (new $relatedModel())->where($otherKey, '=', $this->$foreignKey);
    }

    public function getConnection()
    {
        return Database::getInstance();
    }
}