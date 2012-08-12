<?php

require_once __DIR__.'/../../init.php';

class FormTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $f = new Form('myform');
    $f->addInput('name', 'Имя')
      ->setDefault('123')
      ->setIsRequired(true);
    $f->addInput('email', 'E-mail');
    $res = $f->render();

//    <!-- Form -->
//    <form action="" id="form-myform" method="POST">
//    <!-- Fields -->
//    <label><span>*</span>Имя <input id="form-myform-name" name="myform[name]" type="text" value="123" /></label>
//    <label>E-mail <input id="form-myform-email" name="myform[email]" type="text" value="" /></label>
//    <!-- /Fields -->
//    <div class="submit"><button type="submit">Отправить</button></div>
//    </form>
//    <!-- /Form -->

    $str = '<form action="" id="form-myform"';
    $this->assertTrue(strpos($res, $str) !== false, 'ID формы');

    $str = '<div class="submit"><button type="submit">Отправить</button></div>';
    $this->assertTrue(strpos($res, $str) !== false, 'Кнопка отправки');

    $str = '</form>';
    $this->assertTrue(strpos($res, $str) !== false, '');

    $str = '<label><span>*</span>Имя <input id="form-myform-name" name="myform[name]"';
    $this->assertTrue(strpos($res, $str) !== false, '');

    $str = '<label>E-mail <input id="form-myform-email" name="myform[email]"';
    $this->assertTrue(strpos($res, $str) !== false, '');
  }

  function testVerify()
  {
    $f = new Form('myform');
    $f->addInput('name', 'Имя')
      ->setIsRequired(true);
    $f->addInput('email', 'E-mail')
      ->setRegexp(REGULAR_EMAIL);

    $arr = array('name'=>'', 'email'=>'hello world');
    $f->verify($arr);

    $this->assertTrue($f->hasErrors(), 'В форме должны быть ошибки');

    $err = $f->getErrors();
    $this->assertArrayHasKey('name', $err, 'Массив именован по ключам 1');
    $this->assertArrayHasKey('email', $err, 'Массив именован по ключам 2');

    $str = 'Обязательное поле "Имя" не заполнено'."\n"
          .'Поле "E-mail" заполнено некорректно'."\n";
    $this->assertEquals($str, $f->getErrors(true), 'Текстовое представление ошибок');

    $this->assertFalse($f->getData(), 'В форме есть ошибки - данные недоступны');

    $clean = $arr = array('name' => 'Vasya', 'email' => 'vasya@pupkin.ru');
    $arr['not'] = 'in form';
    $f->verify($arr);
    $this->assertFalse($f->hasErrors(), 'Ошибок нет');
    $this->assertEquals($clean, $f->getData(), 'Данные доступны и только ожидаемые');
    $this->assertEquals('Vasya', $f->getData('name'), 'Данные доступны по полям');
  }
}