<?php

abstract class Item extends Standart
{
  protected static $table;

  const ERROR_NO_ID    = 1;
  const ERROR_NO_TABLE = 2;

  protected static $errors_arr = array(
    self::ERROR_NO_ID    => 'Не указан ID элемента',
    self::ERROR_NO_TABLE => 'Не указана таблица элемента'
  );

  function __construct($id = null)
  {
    if ($id) {
      $this->set('id', $id);
    }
  }

  /** Сохранение данных в БД. */
  public function save()
  {
    if (empty(static::$table)) {
      self::ThrowError(self::ERROR_NO_TABLE);
    }

    if ($id = $this->get('id')) {
      SQL::Update(static::$table)
        ->setArray($this->vars)
        ->where($id)
        ->execute();
    } else {
      $id = SQL::Insert(static::$table)
        ->setArray($this->vars)
        ->execute();

      $this->load($id);
    }

    return $this;
  }

  /**
   * Загрузка актуальных данных из БД. Затирает всё в $this->vars
   * Возможно когда есть свой ID
   */
  public function load($id = null)
  {
    if (empty(static::$table)) {
      self::ThrowError(self::ERROR_NO_TABLE);
    }

    if (is_null($id)) {
      $id = $this->get('id') or static::ThrowError(self::ERROR_NO_ID);
    }

    $this->vars = SQL::Select(static::$table)
      ->where($id)
      ->fetchOne();

    return $this;
  }

  /** Загрузка данных из массива */
  public function fromArray(array $data)
  {
    foreach ($data as $key=>$val) {
      $this->vars[$key] = $val;
    }

    return $this;
  }

  /** Массив значений */
  public function toArray()
  {
    return $this->vars;
  }

  /** Найти элемент */
  public static function Find($where)
  {
    if (empty(static::$table)) {
      self::ThrowError(self::ERROR_NO_TABLE);
    }

    return SQL::Select(static::$table)
      ->where($where)
      ->limit(1)
      ->fetchObject(get_called_class());
  }

  /** Создание элемента из массива */
  public static function Create($data)
  {
    $item = new static;

    return $item->fromArray($data)->save();
  }
}