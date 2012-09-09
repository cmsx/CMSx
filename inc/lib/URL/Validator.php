<?php

class URLValidator extends StandartErrors
{
  /** @var URL */
  protected $url;
  protected $param;
  protected $arguments;
  protected $mapping;
  protected $filter;
  protected $required;
  protected $allowed;
  protected $all_allowed;
  protected $allowed_args = 2;
  protected $is_valid = true;

  const ERROR_REGEXP     = 1;
  const ERROR_FILTER     = 2;
  const ERROR_TOO_MANY   = 11;
  const ERROR_NOT_ENOUGH = 12;
  protected static $errors_arr
    = array(
      self::ERROR_REGEXP     => 'URL не соответствует регулярному выражению',
      self::ERROR_FILTER     => 'URL не соответствует фильтру',
      self::ERROR_TOO_MANY   => 'Переданы лишние параметры',
      self::ERROR_NOT_ENOUGH => 'Переданы не все обязательные параметры',
    );
  protected static $errors_exception = 'URLValidatorException';

  function __construct()
  {
    $this->init();
  }

  public function validate(URL $url)
  {
    $this->is_valid  = true;
    $this->url       = $url;
    $this->param     = $url->getParameters();
    $this->arguments = $url->getArguments();
    $this->processAllowed() && $this->processMapping() && $this->processRequired() && $this->processFilter();
    return $this->param;
  }

  public function isValid()
  {
    return $this->is_valid;
  }

  protected function processAllowed()
  {
    if (!$this->all_allowed) {
      if (count($this->arguments) > $this->allowed_args) {
        return $this->is_valid = false;
      }
      if ($this->param) {
        foreach ($this->param as $name=> $val) {
          if (!$this->allowed || !array_key_exists($name, $this->allowed)) {
            return $this->is_valid = false;
          }
        }
      }
    }
    return true;
  }

  protected function processMapping()
  {
    if ($this->mapping) {
      foreach ($this->mapping as $num=> $name) {
        $this->param[$name] = $this->url->getArgument($num);
      }
    }
    return true;
  }

  protected function processFilter()
  {
    if ($this->filter) {
      foreach ($this->filter as $name=> $filter) {
        if (!isset($this->param[$name])) {
          continue;
        }
        $val = $this->param[$name];
        if (is_callable($filter)) {
          if (!$filter($val)) {
            return $this->is_valid = false;
          }
        } else {
          if (!preg_match($filter, $val)) {
            return $this->is_valid = false;
          }
        }
      }
    }
    return true;
  }

  protected function processRequired()
  {
    if ($this->required) {
      foreach ($this->required as $name=> $not_used) {
        if (empty($this->param[$name])) {
          return $this->is_valid = false;
        }
      }
    }
    return true;
  }

  /** Функция для базовой настройки при наследовании */
  protected function init()
  {
  }

  /**
   * Назначение аргумента из URL в параметр
   */
  public function setMapping($num, $name)
  {
    $this->mapping[$num] = $name;
    return $this;
  }

  /** Проверка параметра URL с помощью callback функции */
  public function setFilter($name, $filter)
  {
    $this->filter[$name] = $filter;
    $this->setAllowed($name);
    return $this;
  }

  /** Установка обязательного параметра */
  public function setRequired($name, $on = true)
  {
    $this->setAllowed($name, $on);
    if ($on) {
      $this->required[$name] = true;
    } elseif (isset($this->required[$name])) {
      unset($this->required[$name]);
    }
    return $this;
  }

  /** Разрешение параметра */
  public function setAllowed($name, $on = true)
  {
    if ($on) {
      $this->allowed[$name] = true;
    } elseif (isset($this->required[$name])) {
      unset($this->allowed[$name]);
    }
    return $this;
  }

  /** Разрешение любых параметров на URL */
  public function setAllAllowed($on = true)
  {
    $this->allowed = (bool)$on;
    return $this;
  }

  /** Допустимое количество аргументов. По-умолчанию = 2 */
  public function setAllowedArgsNum($num)
  {
    $this->allowed_args = $num;
    return $this;
  }
}