<?php

class URL
{
  protected $url;
  protected $arguments;
  protected $parameters;

  function __construct($url = null)
  {
    if (!is_null($url)) {
      $this->parseURL($url);
    }
  }

  function __toString()
  {
    return $this->toString();
  }

  public function getArgument($num, $default = false)
  {
    return $this->hasArgument($num) ? $this->arguments[$num] : $default;
  }

  public function getParameter($name, $filter = null, $default = false)
  {
    if (!$this->hasParameter($name)) {
      return $default;
    }

    $val = $this->parameters[$name];
    if (!empty($filter)) {
      if (is_callable($filter)) {
        return call_user_func_array($filter, array($val)) ? $val : $default;
      } else {
        return preg_match($filter, $val) ? $val : $default;
      }
    }

    return $val;
  }

  public function hasParameter($name)
  {
    return isset($this->parameters[$name]);
  }

  public function hasArgument($num)
  {
    return isset($this->arguments[$num]);
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function getParameters()
  {
    return $this->parameters;
  }

  public function clearArguments()
  {
    $this->arguments = null;

    return $this;
  }

  public function clearParameters()
  {
    $this->parameters = null;

    return $this;
  }

  public function addParameter($name, $val)
  {
    $old = $this->getParameter($name, null, null);
    if (is_null($old)) {
      $old = $val;
    } else {
      if (is_array($old)) {
        $old[] = $val;
      } else {
        $old = array($old, $val);
      }
    }
    $this->parameters[$name] = $old;

    return $this;
  }

  public function addArgument($val)
  {
    $this->arguments[] = $val;

    return $this;
  }

  public function setParameter($name, $val = null)
  {
    if (is_null($val)) {
      unset($this->parameters[$name]);
    } else {
      $this->parameters[$name] = $val;
    }

    return $this;
  }

  public function setArgument($num, $val = null)
  {
    if (is_null($val)) {
      unset($this->arguments[$num]);
    } else {
      $this->arguments[$num] = $val;
    }
  }

  public function parseURL($url = null)
  {
    $this->url = is_null($url) ? $_SERVER['REQUEST_URI'] : $url;
    list($this->arguments, $this->parameters) = self::Parse($this->url);

    return $this;
  }

  public function toString()
  {
    return static::Build($this->arguments, $this->parameters);
  }

  public function toHTML($text, $attr = null, $target = null)
  {
    return HTML::A($this->toString(), $text, $attr, $target);
  }

  /** @return array [arguments, parameters] */
  static public function Parse($string)
  {
    $arguments = $params = array();

    //Если открыта главная страница - URL пуст
    if (empty ($string) || $string == '/') {
      return array(null, null);
    }

    //Если указаны доп.параметры - отсекаем и не учитываем при разборе
    if ($pos = strpos($string, '?')) {
      $string = substr($string, 0, $pos);
    }

    //РАЗБИРАЕМ URI НА ПАРАМЕТРЫ
    $a = explode('/', trim($string, '/'));
    $i = 1;
    if (is_array($a)) {
      foreach ($a as $str) {
        $str = urldecode($str);
        //ЕСЛИ ЕСТЬ ДВОЕТОЧИЕ - РАЗБИРАЕМ КАК ПАРАМЕТР
        if (strpos($str, ':')) {
          $arr = explode(':', $str, 2);
          if (isset ($params[$arr[0]])) {
            if (is_array($params[$arr[0]])) {
              $params[$arr[0]][] = $arr[1];
            } else {
              $params[$arr[0]] = array($params[$arr[0]], $arr[1]);
            }
          } else {
            $params[$arr[0]] = $arr[1];
          }
        } else {
          $arguments[$i++] = $str;
        }
      }
    }

    return array($arguments, $params);
  }

  public static function Build($args, $params = null)
  {
    $url = '/';
    if (is_array($args)) {
      $url .= join('/', $args) . '/';
    }

    if (is_array($params)) {
      foreach ($params as $name => $val) {
        if (is_array($val)) {
          foreach ($val as $v) {
            $url .= $name . ':' . $v . '/';
          }
        } else {
          $url .= $name . ':' . $val . '/';
        }
      }
    }

    return $url;
  }
}