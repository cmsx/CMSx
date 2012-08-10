<?php

require_once __DIR__.'/../../init.php';

class StandartObject extends Standart
{
  public function makeBad($what = null) {
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

  function testArrayAccess()
  {
    $std         = new StandartObject();
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
    $this->assertEquals(array('one'=> 3, 'two'=> 2), $std->get('arr3'), 'Добавление в конец массива ассоциативного массив');
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
    $this->assertEquals(array('two'=> 2, 'one'=> 1), $std->get('arr3'), 'Добавление в начало массива ассоциативного массив');
  }

  function testForeach()
  {
    $this->markTestSkipped('Разобраться с ArrayAccess');
    $std  = new StandartObject();
    $vals = array(
      1 => 'One',
      2 => 'Two',
      3 => 'Three'
    );
    foreach ($vals as $k=> $v) {
      $std[$k] = $v;
    }
    $this->assertTrue(count($std) == 3, 'Количество элементов');
    $i = 1;
    foreach ($std as $k=> $v) {
      echo $v."\n";
      $this->assertTrue($v == next($vals), 'Следующий элемент массива равен оригинальному');
      $this->assertTrue($i++ == $k, 'Ключ массива идет по порядку');
    }
  }
}