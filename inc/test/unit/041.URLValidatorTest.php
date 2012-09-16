<?php

require_once '../init.php';

class URLValidatorTest extends PHPUnit_Framework_TestCase
{
  function testMapping()
  {
    $v = new URLValidator();
    $v->setMapping(1, 'one');
    $v->setMapping(2, 'two');

    $url1 = new URL('/hello/world/');

    $exp = array('one'=>'hello', 'two'=>'world');
    $act1 = $v->validate($url1);
    $this->assertEquals($exp, $act1, 'Базовый маппинг');
    $this->assertTrue($v->isValid(), 'Валидация проходит');
  }

  function testRequired()
  {
    $v = new URLValidator();
    $v->setRequired('you');

    $v->validate(new URL('/hello/'));
    $this->assertFalse($v->isValid(), 'Без обязательного параметра URL неверный');

    $v->validate(new URL('/hello/you:me/'));
    $this->assertTrue($v->isValid(), 'URL с обязательным параметром верный');

    $v->validate(new URL('/hello/you:me/me:you/'));
    $this->assertFalse($v->isValid(), 'URL с обязательным и лишним параметром неверный');
  }

  function testValidating()
  {
    $v = new URLValidator();
    $v->setMapping(1, 'one');
    $v->setMapping(2, 'two');

    $url1 = new URL('/hello/world/');
    $url2 = new URL('/hello/man/from:me/to:you/');

    $v->validate($url2);
    $this->assertFalse($v->isValid(), 'Валидация не проходит из-за лишних параметров');

    $v->validate(new URL('/hello/world/one:fake/'));
    $this->assertFalse($v->isValid(), 'Несмотря на маппинг параметр не может быть передан напрямую');

    $v->setAllowed('from');
    $v->setAllowed('to');

    $v->validate($url2);
    $this->assertTrue($v->isValid(), 'Параметры были разрешены - валидация проходит');

    $v->setAllowedArgsNum(1);

    $v->validate(new URL('/hello/'));
    $this->assertTrue($v->isValid(), 'Допускается только один аргумент - проходит');

    $v->validate($url1);
    $this->assertFalse($v->isValid(), 'Допускается только один аргумент - не проходит');

    $v->setFilter('from', 'is_numeric');
    $v->validate($url2);
    $this->assertFalse($v->isValid(), 'Фильтр по числовому значению');
    $v->validate(new URL('/hello/from:123/'));
    $this->assertTrue($v->isValid(), 'Число проходит');

    $v->setFilter('from', '/^[0-9]{3,}$/');
    $v->validate($url2);
    $this->assertFalse($v->isValid(), 'Фильтр по регулярному выражению');

    $v->validate(new URL('/hello/from:12/'));
    $this->assertFalse($v->isValid(), 'Число меньше 3 знаков не проходит');

    $v->validate(new URL('/hello/from:123/'));
    $this->assertTrue($v->isValid(), 'Число из 3+ проходит');
  }
}