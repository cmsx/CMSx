<?php

/**
 * Класс реализует часто используемые геттеры и сеттеры,
 * доступ к объекту как к массиву и хранилку-обработчик ошибок.
 */

abstract class Standart implements ArrayAccess
{
  /** @var array Переменные объекта */
  protected $vars = array();
  /** @var array|null Ошибки */
  protected $errors;

  function __construct()
  {
    $this->init();
  }

  /** Функция для доп.инициализации при наследовании */
  protected function init()
  {
  }

  //Реализация для ArrayAccess
  public function offsetGet($offset)
  {
    return $this->get($offset);
  }

  public function offsetSet($offset, $value)
  {
    return $this->set($offset, $value);
  }

  public function offsetUnset($offset)
  {
    return $this->set($offset);
  }

  public function offsetExists($offset)
  {
    return false !== $this->get($offset);
  }

  /**
   * Установка значения переменной объекта
   * @param $name имя переменной
   * @param null $value значение. Если null то переменная будет удалена
   * @return static
   */
  public function set($name, $value = null)
  {
    if (is_null($value)) {
      unset($this->vars[$name]);
    } else $this->vars[$name] = $value;
    return $this;
  }

  /**
   * Получение переменной объекта.
   * Если указан фильтр и значение не соответствует - вернет $default
   * @param $name имя переменной
   * @param null|string|Closure $filter Фильтр для проверки значения
   * @return mixed|bool
   */
  public function get($name, $filter = null, $default = false)
  {
    if (!isset ($this->vars[$name])) {
      return false;
    }
    if (is_null($filter)) {
      return $this->vars[$name];
    } elseif (is_callable($filter)) {
      return call_user_func_array($filter, array($this->vars[$name]))
        ? $this->vars[$name]
        : $default;
    } elseif (is_string($filter)) {
      return preg_match($filter, $this->vars[$name])
        ? $this->vars[$name]
        : $default;
    } else {
      return false;
    }
  }

  /**
   * Добавление в конец переменной значения.
   * Корректно работает с массивами.
   * @param string $name Имя переменной
   * @param string|array $value Добавляемая строка или массив
   * @return Standart
   */
  public function append($name, $value)
  {
    $v = $this->get($name);
    if (is_array($v)) {
      if (is_array($value)) {
        $v = array_merge($v, $value);
      } else array_push($v, $value);
    } else {
      $v = $v.$value;
    }
    return $this->set($name, $v);
  }

  /**
   * Добавление в начало переменной значения.
   * Корректно работает с массивами.
   * @param string $name Имя переменной
   * @param string|array $value Добавляемая строка или массив
   * @return Standart
   */
  public function prepend($name, $value)
  {
    $v = $this->get($name);
    if (is_array($v)) {
      if (is_array($value)) {
        $v = array_merge($value, $v);
      } else array_unshift($v, $value);
    } else {
      $v = $value.$v;
    }
    return $this->set($name, $v);
  }

  /**
   * Проверка наличия ошибок. Если ошибок нет, вернет false.
   * Если ошибки есть вернет массив ключ=>текст ошибки
   * @param $what ключ для выбора ошибки по заданному ключу
   * @return array|bool
   */
  public function getErrors($what = null)
  {
    if (!$this->hasErrors()) {
      return false;
    }
    if (!is_null($what)) {
      return isset ($this->errors[$what]) ? (array)$this->errors[$what] : false;
    } else {
      return $this->errors;
    }
  }

  /** Проверка есть ли ошибки */
  public function hasErrors()
  {
    return is_array($this->errors) && count($this->errors) > 0;
  }

  /**
   * Добавление ошибки
   * @param $text текст ошибки
   * @param $what ключ
   * @return Standart
   */
  protected function addError($text, $what = null)
  {
    if (!is_null($what)) {
      $this->errors[$what][] = $text;
    } else {
      $this->errors[] = $text;
    }
    return $this;
  }
}