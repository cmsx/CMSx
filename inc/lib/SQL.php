<?php

class SQL extends StandartErrors
{
  /** В объекте нет соединения */
  const ERROR_NO_CONNECTION = 10;
  /** Соединение не является объектом PDO */
  const ERROR_BAD_CONNECTION = 11;
  /** Ошибка при select`е по паре ключ-значение, нет такого ключа */
  const ERROR_SELECT_BY_PAIR_NO_KEY = 20;
  /** Ошибка при select`е по паре ключ-значение, нет такого значения */
  const ERROR_SELECT_BY_PAIR_NO_VALUE = 21;
  /** Ошибка при попытке создания полнотекстового индекса на таблице не MyISAM */
  const ERROR_FULLTEXT_ONLY_MYISAM = 31;

  /** Тип таблиц MyISAM принят по умолчанию в MySQL */
  const TYPE_MyISAM = 'MyISAM';
  /** Таблицы с поддержкой транзакций и блокировкой строк. */
  const TYPE_InnoDB = 'InnoDB';
  /** Данные для этой таблицы хранятся только в памяти. */
  const TYPE_HEAP = 'HEAP';

  protected static $prefix;
  /** @var PDO */
  protected static $connection;

  protected static $errors_exception = 'SQLException';
  protected static $errors_arr = array(
    self::ERROR_NO_CONNECTION           => 'Для запросов не указано соединения',
    self::ERROR_BAD_CONNECTION          => 'Соединение не является объектом PDO',
    self::ERROR_SELECT_BY_PAIR_NO_KEY   => 'В запросе нет ключа "%s"',
    self::ERROR_SELECT_BY_PAIR_NO_VALUE => 'В запросе нет значений "%s"',
    self::ERROR_FULLTEXT_ONLY_MYISAM    => 'Попытка назначения полнотекстового индекса таблице "%s" с типом "%s"',
  );

  /**
   * Задаем префикс по умолчанию для всех запросов
   * @static
   *
   * @param $prefix
   */
  public static function SetPrefix($prefix)
  {
    self::$prefix = $prefix;
  }

  /**
   * @static
   *
   * @param SQLQuery|string $sql
   *
   * @return PDOStatement|bool
   */
  public static function Execute($sql, $values = null)
  {
    if (!self::$connection) {
      self::ThrowError(self::ERROR_NO_CONNECTION);
    }
    if (!(self::$connection instanceof PDO)) {
      self::ThrowError(self::ERROR_BAD_CONNECTION);
    }
    $stmt = self::$connection->prepare($sql);
    return $stmt->execute($values ? $values : null) ? $stmt : false;
  }

  /** Последний добавленный ID */
  public static function GetLastInsertID()
  {
    return self::$connection->lastInsertId();
  }

  /**
   * Подключение по умолчанию для всех запросов
   * @static
   *
   * @param PDO $connection
   */
  public static function SetConnection(PDO $connection)
  {
    self::$connection = $connection;
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQuerySelect
   */
  public static function Select($table)
  {
    return self::Configure(new SQLQuerySelect($table));
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQueryUpdate
   */
  public static function Update($table)
  {
    return self::Configure(new SQLQueryUpdate($table));
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQueryDelete
   */
  public static function Delete($table)
  {
    return self::Configure(new SQLQueryDelete($table));
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQueryCreate
   */
  public static function Create($table)
  {
    return self::Configure(new SQLQueryCreate($table));
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQueryTruncate
   */
  public static function Truncate($table)
  {
    return self::Configure(new SQLQueryTruncate($table));
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQueryInsert
   */
  public static function Insert($table)
  {
    return self::Configure(new SQLQueryInsert($table));
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQueryDrop
   */
  public static function Drop($table)
  {
    return self::Configure(new SQLQueryDrop($table));
  }

  /**
   * @static
   *
   * @param $table
   *
   * @return SQLQueryAlter
   */
  public static function Alter($table)
  {
    return self::Configure(new SQLQueryAlter($table));
  }

  /** Установка подключения и префикса по умолчанию в запрос */
  protected function Configure(SQLQuery $query)
  {
    if (self::$prefix) {
      $query->setPrefix(self::$prefix);
    }
    if (self::$connection) {
      $query->setConnection(self::$connection);
    }
    return $query;
  }

}