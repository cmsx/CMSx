<?php

require_once __DIR__ . '/../../init.php';

class StandartObject extends Standart
{
  protected static $errors_arr = array(
    1 => 'Ошибка ведь!',
    2 => 'Ошибка "%d" с вставленным "%s"',
  );

  public function makeBad($what = null)
  {
    $this->addError('Something Bad', $what);
    return $this->getErrors();
  }
}

class StandartTest extends PHPUnit_Framework_TestCase
{
  function testErrors()
  {
    $std = new StandartObject();

    $this->assertFalse($std->hasErrors(), 'Ошибок еще нет');
    $std->makeBad();
    $this->assertTrue($std->hasErrors(), 'Ошибки уже есть');

    $res = $std->getErrors();
    $this->assertEquals(array('Something Bad'), $res, 'Одна ошибка в массиве');
    $res = $std->makeBad();
    $this->assertEquals(array('Something Bad', 'Something Bad'), $res, 'Две ошибки в массиве');

    $std->makeBad('ooops');
    $this->assertEquals(array('Something Bad'), $std->getErrors('ooops'), 'Именованная ошибка');
  }

  function testStaticErrors()
  {
    try {
      StandartObject::ThrowError(1);
      $this->fail('Выкинуто исключение 1');
    } catch (PHPUnit_Framework_Exception $e) {
      throw $e;
    } catch (Exception $e) {
      $this->assertEquals(1, $e->getCode(), 'Исключение выкинуто с кодом 1');
      $this->assertEquals('Ошибка ведь!', $e->getMessage(), 'Ошибка выкинута с нужным текстом');
    }

    try {
      StandartObject::ThrowError(2, 42, 'текст');
      $this->fail('Выкинуто исключение 2');
    } catch (PHPUnit_Framework_Exception $e) {
      throw $e;
    } catch (Exception $e) {
      $this->assertEquals(2, $e->getCode(), 'Исключение выкинуто с кодом 2');
      $this->assertEquals('Ошибка "42" с вставленным "текст"', $e->getMessage(), 'Текст с подстановками');
    }
  }

  function testArrayAccess()
  {
    $std = new StandartObject();
    $std['name'] = 'One';
    $this->assertEquals('One', $std['name'], 'Установка и чтение значения');
  }

  function testAppend()
  {
    $std = new StandartObject();

    $std->set('title', 'Hello ');
    $std->append('title', 'World!');
    $this->assertEquals('Hello World!', $std->get('title'), 'Добавление в конец текста');

    $std->set('arr', array(1, 2, 3));
    $std->append('arr', 4);
    $this->assertEquals(array(1, 2, 3, 4), $std->get('arr'), 'Добавление в конец массива значения');

    $std->set('arr2', array(1, 2, 3));
    $std->append('arr2', array(4, 5));
    $this->assertEquals(array(1, 2, 3, 4, 5), $std->get('arr2'), 'Добавление в конец массива другой массив');

    $std->set('arr3', array('one'=> 1, 'two'=> 2));
    $std->append('arr3', array('one'=> 3));
    $this->assertEquals(
      array('one'=> 3, 'two'=> 2), $std->get('arr3'), 'Добавление в конец массива ассоциативного массив'
    );
  }

  function testPrepend()
  {
    $std = new StandartObject();

    $std->set('title', 'Hello');
    $std->prepend('title', 'World ');
    $this->assertEquals('World Hello', $std->get('title'), 'Добавление в начало текста');

    $std->set('arr', array(1, 2, 3));
    $std->prepend('arr', 4);
    $this->assertEquals(array(4, 1, 2, 3), $std->get('arr'), 'Добавление в начало массива значения');

    $std->set('arr2', array(1, 2, 3));
    $std->prepend('arr2', array(4, 5));
    $this->assertEquals(array(4, 5, 1, 2, 3), $std->get('arr2'), 'Добавление в начало массива другой массив');

    $std->set('arr3', array('one'=> 1, 'two'=> 2));
    $std->prepend('arr3', array('two'=> 3));
    $this->assertEquals(
      array('two'=> 2, 'one'=> 1), $std->get('arr3'), 'Добавление в начало массива ассоциативного массив'
    );
  }
}