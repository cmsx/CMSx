<?php

class PageError extends Page
{
  protected $http_code;
  protected $valid_codes = array(
    401 => array(
      'message' => 'Для доступа к этой странице нужно авторизоваться',
      'status'  => 'Unauthorized'
    ),
    403 => array(
      'message' => 'Доступ запрещен',
      'status'  => 'Forbidden'
    ),
    404 => array(
      'message' => 'Страница не существует',
      'status'  => 'Not Found'
    ),
    500 => array(
      'message' => 'Ошибка сервера',
      'status'  => 'Internal Server Error'
    ),
    503 => array(
      'message' => 'Ведутся технические работы',
      'status'  => 'Service Unavailable'
    )
  );

  /** Если код ошибки является валидным HTTP кодом он будет выдан в заголовке */
  public function setErrorCode($code)
  {
    if (array_key_exists($code, $this->valid_codes)) {
      $this->http_code = $code;
      $this->set('title', $code.' - '.$this->valid_codes[$code]['message']);
    } else {
      $this->set('title', 'Неизвестная ошибка');
    }
    return $this;
  }

  public function render()
  {
    if ($this->http_code) {
      header(sprintf('HTTP/1.0 %d %s', $this->http_code, $this->valid_codes[$this->http_code]['status']));
    }
    return parent::render();
  }
}