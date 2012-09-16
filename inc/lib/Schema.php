<?php

/**
 * В конструкторе наследующего класса обязательно должны быть
 * заданы имя таблицы и запрос для её создания через SQL::Create()...
 */
abstract class Schema
{
  /** Имя создаваемой таблицы без префикса */
  protected $table;
  /** @var SQLQueryCreate */
  protected $query;

  /** Настройка имени и структуры таблицы */
  function __construct()
  {

  }

  /** Создание таблицы */
  public function createTable($drop = false)
  {
    if (is_null($this->table)) {
      throw new Exception(get_called_class() . ': Имя таблицы не определено', 501);
    }
    if (is_null($this->query)) {
      throw new Exception(get_called_class() . ': SQL для создания таблицы не определен', 501);
    }

    if ($drop) {
      SQL::Drop($this->table);
    }

    return $this->query->execute();
  }

  /** Забивание таблицы стартовым контентом */
  public function fillContent()
  {
    return true;
  }

  /** Имя таблицы в БД */
  public function getTable()
  {
    return $this->table;
  }
}