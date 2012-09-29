<?php

require_once __DIR__.'/../init.php';

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

    $str = '<form action="" id="form-myform"';
    $this->assertTrue(strpos($res, $str) !== false, 'ID формы');

    $str = '<button type="submit">Отправить</button>';
    $this->assertTrue(strpos($res, $str) !== false, 'Кнопка отправки');

    $str = '</form>';
    $this->assertTrue(strpos($res, $str) !== false, 'Закрывающий тэг');

    $str = '<tr><th> <span>*</span> Имя:</th><td><input id="form-myform-name" name="myform[name]"';
    $this->assertTrue(strpos($res, $str) !== false, '');

    $f->setAction('far/far/away')
      ->setSubmitButton('Сохранить');
    $res = $f->render();

    $str = '<form action="far/far/away" id="form-myform"';
    $this->assertTrue(strpos($res, $str) !== false, 'Задан action');

    $str = '<button type="submit">Сохранить</button>';
    $this->assertTrue(strpos($res, $str) !== false, 'Изменена кнопка отправки');
  }

  function testOptionsAndText()
  {
    $f = new Form('test');
    $f->addSelect('city', 'Город')
      ->setOptions(array(
        'msk' => 'Москва',
        'spb' => 'Санкт-Петербург'
      ));
    $f->addInput('name');

    $this->assertEquals('Москва', $f->field('city')->getValueName('msk'), 'Получаем значение из списка по ключу');
    $this->assertFalse($f->field('city')->getValueName('Что-то там'), 'Значения нет в списке опций');
    $this->assertEquals('Что-то там', $f->field('name')->getValueName('Что-то там'), 'Получаем значение без изменений');

    $f->validate(array('city'=>'spb', 'name'=>'John'));

    $this->assertEquals('Санкт-Петербург', $f->field('city')->getValueName(), 'Значение из опций после валидации');
    $this->assertEquals('John', $f->field('name')->getValueName(), 'Значение из поля прошедшего валидацию');

    $exp_html = "<p><b>Город</b>: Санкт-Петербург</p>\n<p><b>Name</b>: John</p>\n";
    $exp_text = "Город: Санкт-Петербург\nName: John\n";
    $this->assertEquals($exp_html, $f->getDataAsText(), 'Данные в виде HTML');
    $this->assertEquals($exp_text, $f->getDataAsText(false), 'Данные в виде plain text');
  }

  function testVerify()
  {
    $f = new Form('myform');
    $f->addInput('name', 'Имя')
      ->setIsRequired(true);
    $f->addInput('email', 'E-mail')
      ->setRegexp(REGULAR_EMAIL);

    $arr = array('name'=>'', 'email'=>'hello world');
    $f->validate($arr);

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
    $f->validate($arr);
    $this->assertFalse($f->hasErrors(), 'Ошибок нет');
    $this->assertEquals($clean, $f->getData(), 'Данные доступны и только ожидаемые');
    $this->assertEquals('Vasya', $f->getData('name'), 'Данные доступны по полям');
  }
}