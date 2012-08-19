<?php

/**
 * @method SQLQueryDelete bind($key, $value) Бинд произвольной переменной в запрос
 * @method SQLQueryDelete bindArray(array $values) Бинд произвольных переменных в запрос из массива ключ-значение
 * @method SQLQueryDelete setPrefix($prefix) Префикс для всех таблиц в запросах
 */
class SQLQueryDelete extends SQLQuery
{
  /**
   * Создание SQL запроса по настроенным параметрам
   */
  public function make($bind_values = false)
  {
    $this->sql = 'DELETE FROM ' . SQLBuilder::QuoteTable($this->table, $this->prefix)
      . SQLBuilder::BuildWhere($this->where, $bind_values, $this->where_and)
      . SQLBuilder::BuildLimit($this->limit, $this->offset);
    if ($bind_values) {
      $this->sql = SQLBuilder::ReplaceBindedValues($this->sql, $this->binded_values);
    }
    return $this->sql;
  }

  /**
   * Выполняет запрос и возвращает количество затронутых строк.
   * false если возникла ошибка
   *
   * @return int|bool
   */
  public function execute($values = null)
  {
    $res = parent::execute($values);
    if ($res) {
      return $res->rowCount();
    } else {
      return false;
    }
  }


  // QUERY SETUP


  /** Ограничение LIMIT */
  public function limit($limit, $offset = null)
  {
    $this->limit = $limit;
    if (!is_null($offset)) {
      $this->offset = $offset;
    }
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

  /** Условие WHERE объединяется AND или OR */
  public function setWhereJoinByAnd($on)
  {
    $this->where_and = (bool)$on;
    return $this;
  }
}