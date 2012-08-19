<?php

class SQLQueryInsert extends SQLQuery
{
  /**
   * Создание SQL запроса по настроенным параметрам
   */
  public function make($bind_values = false)
  {
    if (!isset($this->values['insert'])) {
      return null;
    }
    $columns   = array_keys($this->values['insert']);
    $this->sql = 'INSERT INTO ' . SQLBuilder::QuoteTable($this->table, $this->prefix)
      . ' (' . SQLBuilder::BuildNames($columns) . ')'
      . ' VALUES (' . SQLBuilder::BuildValues($this->values['insert'], $bind_values, 'insert') . ')';
    return $this->sql;
  }

  /** Установка значения для изменения в БД */
  public function set($key, $value)
  {
    $this->values['insert'][$key] = $value;
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
}