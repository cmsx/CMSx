<?php

/**
 * Контроллер должен содержать действия с суффиксом Action. Примеры:
 * /some/ => someController->indexAction()
 * /some/work/ => someController->workAction()
 * На входе действие принимает один параметр - массив параметров
 *
 * Если нужна валидация URL или меппинг параметров для действия создается
 * метод Validator возвращающий объект URLValidator. Примеры:
 * для indexAction => indexValidator()
 * для someAction => someValidator()
 * для всех действий контроллера без своего валидатора defaultValidator()
 */
class Front
{
  protected $url;
  protected $param;
  protected $action;
  protected $method;
  protected $object;
  protected $controller;
  protected $validator_method;

  /** Роутинг на контроллер и обработка результата */
  public function route(URL $url = null)
  {
    $this->processUrl($url);

    try {
      if (!is_callable(array($this->object, $this->method))) {
        throw new Exception(
          sprintf('Метод "%s" в контроллере "%s" не найден', $this->method, $this->controller),
          PageError::NOT_FOUND
        );
      }

      $this->processValidation($url);

      $res = $this->object->{$this->method}($this->param);
      if ($res instanceof Page) {
        echo $res->render();
      } elseif (!empty($res)) {
        echo $res;
      }
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  /** Разбор URL и определение нужных объектов и методов */
  protected function processUrl($url)
  {
    if (is_null($url)) {
      $this->url = new URL();
      $this->url->parseURL();
    } else {
      $this->url = $url;
    }

    try {
      $one = $this->url->getArgument(1);
      if (empty($one)) {
        throw new Exception();
      }
      $this->controller = $one . 'Controller';
      $this->action     = $this->url->getArgument(2) ? : 'index';
      $this->object     = new $this->controller ($this->url, $one, $this->action);
    } catch (Exception $e) {
      $this->controller = 'defaultController';
      $this->action     = $this->url->getArgument(1) ? : 'index';
      $this->object     = new $this->controller ($this->url, 'default', $this->action);
    }
    $this->method           = $this->action . 'Action';
    $this->validator_method = $this->action . 'Validator';
  }

  /** Получаем параметры и производим валидацию если необходимо */
  protected function processValidation()
  {
    $this->param = $this->url->getParameters();

    /** @var $validator URLValidator */
    $validator = null;
    if (method_exists($this->object, $this->validator_method)) {
      $validator = $this->object->{$this->validator_method}();
    } elseif (method_exists($this->object, 'defaultValidator')) {
      $validator = $this->object->defaultValidator();
    }

    if ($validator instanceof URLValidator) {
      $this->param = $validator->validate($this->url);
      if (!$validator->isValid()) {
        throw new Exception('URL не прошел валидацию', 404);
      }
    }
  }

  /** Обработка ошибок и отображение пользователю */
  protected function handleError(Exception $e)
  {
    $p = $this->getPageError()->setErrorCode($e->getCode());

    if ($p->isRedirect()) {
      header('Location: '.$e->getMessage());
      $p->sendHTTPCode();
      return;
    }

    if (DEVMODE) {
      $p->set('message', $e->getMessage());
      $p->set('stack', $e->getTraceAsString());
    }
    echo $p->render();
  }

  /** Объект страницы с ошибкой */
  protected function getPageError()
  {
    return new PageError('default/error.php');
  }
}