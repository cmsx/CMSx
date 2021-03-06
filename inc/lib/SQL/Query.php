<?php

abstract class SQLQuery
{
  /** @var PDO */
  protected $connection;
  /** @var PDOStatement */
  protected $statement;

  protected $sql;
  protected $join;
  protected $limit;
  protected $table;
  protected $where;
  protected $prefix;
  protected $values;
  protected $offset;
  protected $having;
  protected $columns;
  protected $orderby;
  protected $groupby;
  protected $binded_values;
  protected $where_and = true;
  protected $having_and = true;
  protected $last_insert_id;

  function __construct($table)
  {
    $this->table = $table;
  }

  function __toString()
  {
    return $this->make(true);
  }

  function __invoke()
  {
    return $this->execute();
  }

  /**
   * Создание SQL запроса по настроенным параметрам
   *
   * @abstract
   */
  abstract public function make($bind_values = false);

  /**
   * Выполнение запроса
   * @return PDOStatement
   * @throws SQLException
   */
  public function execute($values = null)
  {
    if (is_array($values)) {
      $this->bindArray($values);
    }
    $this->make();
    $this->statement = SQL::Execute($this);
    return $this->statement;
  }

  /** Бинд произвольной переменной в запрос */
  public function bind($key, $value)
  {
    if (substr($key, 0, 1) != ':') {
      $key = ':' . $key;
    }
    $this->binded_values[$key] = $value;
    return $this;
  }

  /** Бинд произвольных переменных в запрос из массива ключ-значение */
  public function bindArray(array $values)
  {
    foreach ($values as $key => $val) {
      $this->bind($key, $val);
    }
    return $this;
  }

  /**
   * Получение массива значений разбитых по частям запроса
   */
  public function getValues()
  {
    return $this->values;
  }

  /**
   * Последний сгенерированный запрос в том виде, каким он был.
   */
  public function getLastSQL()
  {
    return !empty($this->sql) ? $this->sql : false;
  }

  /**
   * Получение массива значений для всех частей запроса в виде для бинда в PDO
   */
  public function getBindedValues()
  {
    $binded_values = $this->binded_values;
    if ($this->values) {
      foreach ($this->values as $part => $arr) {
        foreach ($arr as $key => $val) {
          $binded_values[':' . $part . '_' . $key] = $val;
        }
      }
    }
    return count($binded_values) ? $binded_values : false;
  }

  /** Префикс для всех таблиц в запросах */
  public function setPrefix($prefix)
  {
    $this->prefix = $prefix;
    return $this;
  }

  /** Передача в объект соединения PDO */
  public function setConnection(PDO $conn)
  {
    $this->connection = $conn;
    return $this;
  }

  /** Обработка условия where */
  protected function processWhere()
  {
    if ($this->where) {
      foreach ($this->where as $key => $val) {
        if (is_numeric($key)) {
          if ($val === true || $val === false) {
            unset($this->where[$key]);
            $this->where['is_active'] = true;
          } elseif (is_numeric($val)) {
            unset($this->where[$key]);
            $this->where['id'] = $val;
          }
        }
      }
    }

    if ($this->where) {
      $this->setValues($this->where, 'where');
    }
  }

  /**
   * Установка значения
   * @param $key   - ключ
   * @param $value - значение
   * @param $part  - часть SQL к которой относится
   */
  protected function setValue($key, $value, $part)
  {
    $this->values[$part][$key] = $value;
    return $this;
  }

  /**
   * Установка значений по массиву ключ-значение
   * Если ключ числовой и не запрещено добавление, он будет добавлен с подчеркиванием, напр. :_1
   *
   * @param $array           - массив значений
   * @param $part            - часть SQL к которой относится
   * @param $ignore_num_keys - не добавлять значение, если ключ числовой
   */
  protected function setValues($array, $part, $ignore_num_keys = true)
  {
    foreach ($array as $key => $val) {
      if (is_numeric($key)) {
        if ($ignore_num_keys) {
          continue;
        }
        $key = '_' . $key;
      }
      $this->setValue($key, $val, $part);
    }
    return $this;
  }
}