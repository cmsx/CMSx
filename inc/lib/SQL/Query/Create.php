<?php

class SQLQueryCreate extends SQLQuery
{
  protected $definition = array(
    'type'         => SQL::TYPE_MyISAM,
    'columns'      => array(),
    'index'        => array(),
    'unique'       => array(),
    'fulltext'     => array(),
    'primary_key'  => null,
  );

  /**
   * Создание SQL запроса по настроенным параметрам
   *
   */
  public function make($bind_values = false)
  {
    $parts = array();
    foreach ($this->definition['columns'] as $col=> $def) {
      $parts[] = '`' . $col . '` ' . $def;
    }

    foreach ($this->definition['index'] as $name=> $index) {
      $parts[] = 'INDEX `' . $name . '` (' . SQLBuilder::BuildNames($index) . ')';
    }

    foreach ($this->definition['unique'] as $name=> $index) {
      $parts[] = 'UNIQUE INDEX `' . $name . '` (' . SQLBuilder::BuildNames($index) . ')';
    }

    foreach ($this->definition['fulltext'] as $name=> $index) {
      $parts[] = 'FULLTEXT `' . $name . '` (' . SQLBuilder::BuildNames($index) . ')';
    }

    if (!is_null($this->definition['primary_key'])) {
      $parts[] = 'PRIMARY KEY (' . SQLBuilder::BuildNames($this->definition['primary_key']) . ')';
    }

    $this->sql = 'CREATE TABLE ' . SQLBuilder::QuoteTable($this->table, $this->prefix) . " (\n  "
      . join(",\n  ", $parts) . "\n) TYPE=" . $this->definition['type'];
    return $this->sql;
  }

  /**
   * Получение компонентов запроса
   *
   * * type - тип таблицы SQL::TYPE_*
   *
   * * columns - столбцы для создания
   *
   * * index - массив имя индекса => набор столбцов
   *
   * * unique - массив имя уникального индекса => набор столбцов
   *
   * * fulltext - полнотекстовый индекс (только для MyISAM)
   *
   * * primary_key - столбцы для первичного ключа
   */
  public function getDefinition($component = null)
  {
    if (is_null($component)) {
      return $this->definition;
    }
    return isset($this->definition[$component]) ? $this->definition[$component] : null;
  }

  public function add($column, $definition)
  {
    $this->definition['columns'][$column] = $definition;
    return $this;
  }

  public function addPrimaryKey($columns, $_ = null)
  {
    if (is_array($columns) || is_null($columns)) {
      $index = $columns;
    } else {
      $index = func_get_args();
    }
    $this->definition['primary_key'] = $index;
    return $this;
  }

  public function addIndex($columns, $_ = null)
  {
    if (is_array($columns) || is_null($columns)) {
      $index = $columns;
    } else {
      $index = func_get_args();
    }
    $this->definition['index']['i_' . join('_', $index)] = $index;
    return $this;
  }

  public function addUniqueIndex($columns, $_ = null)
  {
    if (is_array($columns) || is_null($columns)) {
      $index = $columns;
    } else {
      $index = func_get_args();
    }
    $this->definition['unique']['u_' . join('_', $index)] = $index;
    return $this;
  }

  public function addFulltextIndex($columns, $_ = null)
  {
    if ($this->definition['type'] != SQL::TYPE_MyISAM) {
      SQL::ThrowError(SQL::ERROR_FULLTEXT_ONLY_MYISAM, $this->table, $this->definition['type']);
    }
    if (is_array($columns) || is_null($columns)) {
      $index = $columns;
    } else {
      $index = func_get_args();
    }
    $this->definition['fulltext']['f_' . join('_', $index)] = $index;
    return $this;
  }

  /**
   * @param $type - тип таблицы: MyISAM, InnoDB и т.п.
   */
  public function setType($type)
  {
    if (!empty($this->definition['fulltext']) && $type != SQL::TYPE_MyISAM) {
      SQL::ThrowError(SQL::ERROR_FULLTEXT_ONLY_MYISAM, $this->table, $type);
    }
    $this->type = $type;
    return $this;
  }

  // ЧАСТО ИСПОЛЬЗУЕМЫЕ ТИПЫ ПОЛЕЙ

  /** id - INT UNSIGNED AUTO_INCREMENT */
  public function addId($col = null)
  {
    return $this
      ->add(($col ? $col : 'id'), 'INT UNSIGNED AUTO_INCREMENT')
      ->addPrimaryKey('id');
  }

  /** parent_id - INT UNSIGNED DEFAULT NULL */
  public function addParentId($col = null)
  {
    return $this->add(($col ? $col : 'parent_id'), 'INT UNSIGNED DEFAULT NULL');
  }

  /** price - FLOAT(10,2) */
  public function addPrice($col = null)
  {
    return $this->add(($col ? $col : 'price'), 'FLOAT(10,2)');
  }

  /** text - $long ? LONGTEXT : TEXT */
  public function addText($col = null, $long = false)
  {
    return $this->add(($col ? $col : 'text'), ($long ? 'LONGTEXT' : 'TEXT'));
  }

  /** BOOL DEFAULT $default = 0 */
  public function addBool($col, $default = 0)
  {
    return $this->add($col, 'BOOL DEFAULT '.$default);
  }

  /** TIMESTAMP DEFAULT 0 */
  public function addTime($col)
  {
    return $this->add($col, 'TIMESTAMP DEFAULT 0');
  }

  /** TIMESTAMP DEFAULT CURRENT_TIMESTAMP */
  public function addTimeCreated($col)
  {
    return $this->add($col, 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
  }

  /** TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP */
  public function addTimeUpdated($col)
  {
    return $this->add($col, 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
  }

  /** VARCHAR( $length ? $length : 250 ) DEFAULT NULL */
  public function addChar($col, $length = null)
  {
    return $this->add($col, 'VARCHAR(' . ($length ? $length : 250) . ') DEFAULT NULL');
  }

  /** INT UNSIGNED DEFAULT 0 */
  public function addInt($col)
  {
    return $this->add($col, 'INT UNSIGNED DEFAULT 0');
  }

  /** TINYINT UNSIGNED DEFAULT 0 */
  public function addTinyInt($col)
  {
    return $this->add($col, 'TINYINT UNSIGNED DEFAULT 0');
  }
}