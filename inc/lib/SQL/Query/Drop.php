<?php

class SQLQueryDrop extends SQLQuery
{
  protected $if_exists = true;

  /**
   * Создание SQL запроса по настроенным параметрам
   */
  public function make($bind_values = false)
  {
    $this->sql = 'DROP TABLE '
      . ($this->if_exists ? 'IF EXISTS ' : '')
      . SQLBuilder::QuoteTable($this->table, $this->prefix);
    return $this->sql;
  }

  /** Нужно ли добавлять IF EXISTS */
  public function setIfExists($on)
  {
    $this->if_exists = $on;
    return $this;
  }
}