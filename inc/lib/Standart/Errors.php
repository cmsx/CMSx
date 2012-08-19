<?php

abstract class StandartErrors
{
  /** Массив с расшифровками текстов ошибок */
  protected static $errors_arr = array();
  /** Класс Exception`а который будет выбрасываться */
  protected static $errors_exception = 'Exception';

  /**
   * Текст ошибки по коду. Если переданы аргументы, они будут подставлены в sprinf
   *
   * @static
   *
   * @param              $code
   * @param array|string $args
   * @param string       $_
   */
  public static function GetMessage($code, $args = null, $_ = null)
  {
    if (!isset(static::$errors_arr[$code])) {
      return false;
    }
    $msg = static::$errors_arr[$code];
    if (!is_null($args)) {
      if (is_array($args)) {
        array_unshift($args, $msg);
      } else {
        $args    = func_get_args();
        $args[0] = $msg;
      }
      return call_user_func_array('sprintf', $args);
    }
    return $msg;
  }

  /**
   * Выброс ошибки по коду
   */
  public static function ThrowError($code, $args = null, $_ = null)
  {
    $args = func_get_args();
    array_shift($args);
    $msg = static::GetMessage($code, $args);
    throw new static::$errors_exception($msg, $code);
  }
}