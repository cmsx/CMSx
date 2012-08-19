<?php

class SQLQueryTruncate extends SQLQuery
{
  /**
   * Создание SQL запроса по настроенным параметрам
   */
  public function make($bind_values = false)
  {
    $this->sql = 'TRUNCATE TABLE '
      . SQLBuilder::QuoteTable($this->table, $this->prefix);
    return $this->sql;
  }
}