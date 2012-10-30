<?php

class PageError extends Page
{
  /** Необходима авторизация */
  const UNAUTHORIZED = 401;
  /** Доступ запрещен */
  const FORBIDDEN    = 403;
  /** Страница не найдена */
  const NOT_FOUND    = 404;
  /** Ошибка сервера */
  const SERVER_ERROR = 500;
  /** Сервер недоступен */
  const UNAVAILABLE  = 503;
  /** Редирект перманентный */
  const REDIRECT_PERM = 301;
  /** Редирект временный */
  const REDIRECT_TEMP = 302;

  protected $http_code;
  protected $valid_codes = array(
    self::UNAUTHORIZED => array(
      'message' => 'Для доступа к этой странице нужно авторизоваться',
      'status'  => 'Unauthorized'
    ),
    self::FORBIDDEN => array(
      'message' => 'Доступ запрещен',
      'status'  => 'Forbidden'
    ),
    self::NOT_FOUND => array(
      'message' => 'Страница не существует',
      'status'  => 'Not Found'
    ),
    self::SERVER_ERROR => array(
      'message' => 'Ошибка сервера',
      'status'  => 'Internal Server Error'
    ),
    self::UNAVAILABLE => array(
      'message' => 'Ведутся технические работы',
      'status'  => 'Service Unavailable'
    ),
    self::REDIRECT_TEMP => array(
      'message' => 'Страница временно перемещена',
      'status'  => 'Moved Temporarily'
    ),
    self::REDIRECT_PERM => array(
      'message' => 'Страница перемещена',
      'status'  => 'Moved Permanently'
    )
  );

  /** Является ли вызов редиректом */
  public function isRedirect()
  {
    return in_array($this->http_code, array(self::REDIRECT_PERM, self::REDIRECT_TEMP));
  }

  /** Если код ошибки является валидным HTTP кодом он будет выдан в заголовке */
  public function setErrorCode($code)
  {
    if (array_key_exists($code, $this->valid_codes)) {
      $this->http_code = $code;
      $this->set('title', $code . ' - ' . $this->valid_codes[$code]['message']);
    } else {
      $this->set('title', 'Неизвестная ошибка');
    }
    return $this;
  }

  /** Отправка кода HTTP */
  public function sendHTTPCode()
  {
    if ($this->http_code) {
      header(sprintf('HTTP/1.0 %d %s', $this->http_code, $this->valid_codes[$this->http_code]['status']));
    }
  }

  public function render()
  {
    $this->sendHTTPCode();
    return parent::render();
  }
}