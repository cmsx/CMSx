<?php

/**
 * @method SQLQueryUpdate bind($key, $value) Бинд произвольной переменной в запрос
 * @method SQLQueryUpdate bindArray(array $values) Бинд произвольных переменных в запрос из массива ключ-значение
 * @method SQLQueryUpdate setPrefix($prefix) Префикс для всех таблиц в запросах
 */
class SQLQueryUpdate extends SQLQuery
{
  /**
   * Создание SQL запроса по настроенным параметрам
   */
  public function make($bind_values = false)
  {
    if (!isset($this->values['set'])) {
      return null;
    }
    $this->sql = 'UPDATE ' . SQLBuilder::QuoteTable($this->table, $this->prefix)
      . SQLBuilder::BuildSet($this->values['set'], $bind_values)
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

  /** Установка значения для изменения в БД */
  public function set($key, $value)
  {
    $this->values['set'][$key] = $value;
    return $this;
  }

  /** Установка присвоения выражением, например: `key`=now() */
  public function setExpression($expression)
  {
    $this->values['set'][] = $expression;
    return $this;
  }

  /** Установка данных для изменения в БД по массиву ключ-значение */
  public function setArray(array $array)
  {
    foreach ($array as $key => $val) {
      $this->set($key, $val);
    }
    return $this;
  }

  /** Условие WHERE объединяется AND или OR */
  public function setWhereJoinByAnd($on)
  {
    $this->where_and = (bool)$on;
    return $this;
  }
}