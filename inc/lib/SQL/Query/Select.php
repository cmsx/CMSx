<?php

/**
 * @method SQLQuerySelect bind($key, $value) Бинд произвольной переменной в запрос
 * @method SQLQuerySelect bindArray(array $values) Бинд произвольных переменных в запрос из массива ключ-значение
 * @method SQLQuerySelect setPrefix($prefix) Префикс для всех таблиц в запросах
 */
class SQLQuerySelect extends SQLQuery
{
  public function make($bind_values = false)
  {
    $this->sql = 'SELECT '
      . ($this->columns ? SQLBuilder::BuildNames($this->columns) : '*')
      . ' FROM ' . SQLBuilder::QuoteTable($this->table, $this->prefix)
      . SQLBuilder::BuildJoin($this->join)
      . SQLBuilder::BuildWhere($this->where, $bind_values, $this->where_and)
      . SQLBuilder::BuildGroupBy($this->groupby)
      . SQLBuilder::BuildHaving($this->having, $bind_values, $this->having_and)
      . SQLBuilder::BuildOrderBy($this->orderby)
      . SQLBuilder::BuildLimit($this->limit, $this->offset);
    if ($bind_values) {
      $this->sql = SQLBuilder::ReplaceBindedValues($this->sql, $this->binded_values);
    }
    return $this->sql;
  }


  // FETCHING


  /** Получение следующего элемента из запроса в виде массива. */
  public function fetch()
  {
    if (!$this->statement) {
      $this->execute();
    }
    $res = $this->statement->fetch(PDO::FETCH_ASSOC);
    return $res ? $res : false;
  }

  /** Получение всех элементов полученных запросом */
  public function fetchAll()
  {
    if (!$this->statement) {
      $this->execute();
    }
    $res = $this->statement->fetchAll(PDO::FETCH_ASSOC);
    return $res ? $res : false;
  }

  /** Получение следующего элемента из запроса в виде объекта */
  public function fetchObject($class, $constructor_parameters = null)
  {
    if (!$this->statement) {
      $this->execute();
    }
    $this->statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE);
    $res = $this->statement->fetchObject($class, $constructor_parameters);
    return $res ? $res : false;
  }

  /** Получение одного элемента по запросу. Автоматически ставит LIMIT 1 */
  public function fetchOne()
  {
    $this->limit(1);
    $this->execute();
    $res = $this->statement->fetch(PDO::FETCH_ASSOC);
    return $res ? current($res) : false;
  }

  /** Получение массива ключ-значение */
  public function fetchAllByPair($key, $value)
  {
    $res = $this->fetchAll();
    $out = array();
    if (!array_key_exists($key, current($res))) {
      SQL::ThrowError(SQL::ERROR_SELECT_BY_PAIR_NO_KEY, $key);
    }
    if (!array_key_exists($value, current($res))) {
      SQL::ThrowError(SQL::ERROR_SELECT_BY_PAIR_NO_VALUE, $value);
    }
    foreach ($res as $row) {
      $out[$row[$key]] = $row[$value];
    }
    return $out;
  }

  /** Получение всех элементов по запросу в указанный объект */
  public function fetchAllInObject($class, $constructor_parameters = null)
  {
    $out = array();
    while ($obj = $this->fetchObject($class, $constructor_parameters)) {
      $out[] = $obj;
    }
    return $out;
  }

  // QUERY SETUP


  /** Столбцы для выборки. Массив или перечисление столбцов. */
  public function columns($columns, $_ = null)
  {
    if (is_array($columns) || is_null($columns)) {
      $this->columns = $columns;
    } else {
      $this->columns = func_get_args();
    }
    return $this;
  }

  /** Объединение таблиц */
  public function join($table, $on, $type = null)
  {
    $this->join[$table] = array(
      'table' => $this->prefix . $table,
      'on'    => $on,
      'type'  => $type,
    );
    return $this;
  }

  /** Условие WHERE. Массив или перечисление условий. */
  public function where($where, $_ = null)
  {
    if (is_array($where) || is_null($where)) {
      $this->where = $where;
    } else {
      $this->where = func_get_args();
    }
    $this->processWhere();
    return $this;
  }

  /** Сортировка. Массив или перечисление условий. */
  public function orderby($orderby, $_ = null)
  {
    if (is_array($orderby) || is_null($orderby)) {
      $this->orderby = $orderby;
    } else {
      $this->orderby = func_get_args();
    }
    return $this;
  }

  /** Объединение. Массив или перечисление условий. */
  public function groupby($groupby, $_ = null)
  {
    if (is_array($groupby) || is_null($groupby)) {
      $this->groupby = $groupby;
    } else {
      $this->groupby = func_get_args();
    }
    return $this;
  }

  /** Условие HAVING. Массив или перечисление условий. */
  public function having($having, $_ = null)
  {
    if (is_array($having) || is_null($having)) {
      $this->having = $having;
    } else {
      $this->having = func_get_args();
    }
    if ($this->having) {
      $this->setValues($this->having, 'having');
    }
    return $this;
  }

  /** Ограничение LIMIT */
  public function limit($limit, $offset = null)
  {
    $this->limit = $limit;
    if (!is_null($offset)) {
      $this->offset = $offset;
    }
    return $this;
  }

  /** Формирование ограничения LIMIT для постраничности */
  public function page($page, $onpage)
  {
    $this->limit($onpage, (($page - 1) * $onpage));
    return $this;
  }

  /** Условие WHERE объединяется AND или OR */
  public function setWhereJoinByAnd($on)
  {
    $this->where_and = (bool)$on;
    return $this;
  }

  /** Условие HAVING объединяется AND или OR */
  public function setHavingJoinByAnd($on)
  {
    $this->having_and = (bool)$on;
    return $this;
  }
}