<?php

class Controller
{
  /** Имя контроллера */
  protected $controller;
  /** Имя текущего экшна */
  protected $action;
  /** @var URL текущий урл */
  protected $url;

  function __construct(URL $url, $controller, $action)
  {
    $this->url        = $url;
    $this->controller = $controller;
    $this->action     = $action;
  }

  /** Для любого произвольного экшна */
  function __call()
  {
    PageError::NotFound();
  }

  /** Редирект на указанный URL. $permanently - редирект временный или постоянный */
  public function redirect($url, $permanently = true)
  {
    throw new Exception($url, $permanently ? PageError::REDIRECT_PERM : PageError::REDIRECT_TEMP);
  }

  /** Редирект на предыдущую страницу */
  public function back()
  {
    $this->redirect($_SERVER['HTTP_REFERER'] ? : '/', false);
  }

  /** Текущее действие */
  public function getAction()
  {
    return $this->action;
  }

  /** Текущий контроллер */
  public function getController()
  {
    return $this->controller;
  }
}