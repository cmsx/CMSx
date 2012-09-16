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
  static public function HandleError(Exception $e)
  {
    $p = new PageError('error.php');
    $p->setErrorCode($e->getCode());
    if (DEVMODE) {
      $p->set('message', $e->getMessage());
      $p->set('stack', $e->getTraceAsString());
    }
    echo $p->render();
  }

  static public function Route(URL $url = null)
  {
    if (is_null($url)) {
      $url = new URL();
      $url->parseURL();
    }

    $act_num = 1;
    try {
      $one = $url->getArgument(1);
      if (empty($one)) {
        throw new Exception();
      }
      $controller = $one . 'Controller';
      $object     = new $controller;
      $act_num    = 2;
    } catch (Exception $e) {
      $controller = 'defaultController';
      $object     = new $controller;
    }
    $action           = $url->getArgument($act_num) ? : 'index';
    $method           = $action . 'Action';
    $validator_method = $action . 'Validator';

    try {
      if (!is_callable(array($object, $method))) {
        throw new Exception(
          sprintf('Метод "%s" в контроллере "%s" не найден', $method, $controller ? : 'default'), 404
        );
      }

      $param = $url->getParameters();

      /** @var $validator URLValidator */
      $validator = null;
      if (method_exists($object, $validator_method)) {
        $validator = $object->$validator_method();
      } elseif (method_exists($object, 'defaultValidator')) {
        $validator = $object->defaultValidator();
      }

      if ($validator instanceof URLValidator) {
        $param = $validator->validate($url);
        if (!$validator->isValid()) {
          throw new Exception('URL не прошел валидацию', 404);
        }
      }

      $res = $object->$method($param);
      if ($res instanceof Page) {
        echo $res->render();
      } elseif (!empty($res)) {
        echo $res;
      }
    } catch (Exception $e) {
      self::HandleError($e);
    }
  }
}