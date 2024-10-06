<?php 

require_once 'Model.php';

class QueryBuilder
{
    protected $model;

    protected $wheres = [];
    protected $joins = [];
    protected $aggregates = [];
    protected $groupBy = [];
    protected $orderBy = [];
    protected $select = '*';
    protected $selectAlias = '';
    protected $take;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function insert($data)
    {
        if (empty($data)) {
            return false;
        }

        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO {$this->model->getTable()} ($columns) VALUES ($values)";
        $stmt = $this->model->getConnection()->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function update($data)
    {
        $setClause = '';
        
        foreach ($data as $column => $value) {
            $setClause .= "$column=:$column, ";
        }

        $setClause = rtrim($setClause, ', ');

        $whereClause = !empty($this->wheres) ? $this->buildWhereClause($this->wheres) : '';

        $query = "UPDATE {$this->model->getTable()} SET $setClause $whereClause";

        $stmt = $this->model->getConnection()->prepare($query);

        if (!$stmt) {
            return false;
        }

        foreach ($data as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        return $stmt->execute();
    }

    public function delete()
    {
        $whereClause = !empty($this->wheres) ? $this->buildWhereClause($this->wheres) : '';

        $query = "DELETE FROM {$this->model->getTable()} $whereClause";

        $stmt = $this->model->getConnection()->prepare($query);

        if (!$stmt) {
            return false;
        }

        return $stmt->execute();
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

    private function bindWhereParameters($stmt)
    {
        foreach ($this->wheres as $condition) {
            $stmt->bindValue(":{$condition['column']}", $condition['value']);
        }
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
        return $this->executeQuery();
    }

    public function first()
    {
        return $this->executeQuery(true);
    }

    public function query($sql, $bindings = [])
    {
        $stmt = $this->model->getConnection()->prepare($sql);
        $stmt->execute($bindings);

        return strpos(strtoupper($sql), 'SELECT') === 0 ? $stmt->fetchAll(PDO::FETCH_CLASS) : $stmt->rowCount();
    }

    public function paginate($perPage = 10, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        $countQuery = $this->buildCountQuery();
        $totalCount = $this->model->getConnection()->query($countQuery)->fetch(PDO::FETCH_ASSOC)['count'];
        $query = $this->buildQuery() . " LIMIT $perPage OFFSET $offset";
        $stmt = $this->model->getConnection()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_class($this->model));
        $items = $stmt->fetchAll(PDO::FETCH_CLASS);

        return new Paginator($items, $perPage, $page, $totalCount);
    }

    private function buildQuery($singleResult = false)
    {
        $columns = $this->select ? $this->select : '*';
        $whereClause = !empty($this->wheres) ? $this->buildWhereClause($this->wheres) : '';
        $joins = $this->buildJoinClause($this->joins);
        $aggregates = $this->aggregates ? implode(', ', $this->aggregates) : '';
        $groupBy = !empty($this->groupBy) ? 'GROUP BY ' . implode(', ', $this->groupBy) : '';
        $limit = $singleResult ? 'LIMIT 1' : (!empty($this->take) ? 'LIMIT ' . $this->take : '');
        $orderBy = !empty($this->orderBy) ? 'ORDER BY ' . $this->orderBy['column'] . ' ' . strtoupper($this->orderBy['direction']) : '';

        $select = $this->select;
        if ($this->selectAlias) {
            $select .= " as {$this->selectAlias}";
        }

        $table = $this->model->getTable();
        if ($this->model->getTableAlias()) {
            $table .= " as {$this->model->getTableAlias()}";
        }

        return "SELECT $select $aggregates FROM $table $joins $whereClause $groupBy $orderBy $limit";
    }
    
    private function executeQuery($singleResult = false)
    {
        $query = $this->buildQuery($singleResult);
        $stmt = $this->model->getConnection()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_class($this->model));

        return $singleResult ? $stmt->fetch(PDO::FETCH_CLASS) : $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    private function buildCountQuery()
    {
        $joins = $this->buildJoinClause($this->joins);
        $whereClause = !empty($this->wheres) ? $this->buildWhereClause($this->wheres) : '';
        $groupBy = !empty($this->groupBy) ? 'GROUP BY ' . implode(', ', $this->groupBy) : '';

        $table = $this->model->getTable();
        if ($this->model->getTableAlias()) {
            $table .= " as {$this->model->getTableAlias()}";
        }

        return "SELECT COUNT(*) as count FROM $table $joins $whereClause $groupBy";
    }

    protected function buildWhereClause($wheres)
    {
        $where = 'WHERE ';
        foreach ($wheres as $condition) {
            if ($condition['operator'] === 'BETWEEN' && is_array($condition['value'])) {
                $where .= "{$condition['column']} {$condition['operator']} '{$condition['value'][0]}' AND '{$condition['value'][1]}' AND ";
            } else {
                $where .= "{$condition['column']} {$condition['operator']} '{$condition['value']}' AND ";
            }
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
}