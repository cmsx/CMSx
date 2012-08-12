<?php

require_once __DIR__.'/../../init.php';
class TestFormElement extends FormElement
{
}

class FormElementTest extends PHPUnit_Framework_TestCase
{
  function testConstruct()
  {
    $el = new TestFormElement('name', 'Имя', 'myform');
    $this->assertEquals('name', $el->getField(), 'Имя поля');
    $this->assertEquals('Имя', $el->getLabel(), 'Название поля');
    $this->assertEquals('myform[name]', $el->getName(), 'Тег name');
    $this->assertEquals('form-myform-name', $el->getId(), 'Тег id');
  }

  function testRender()
  {
    $el = new TestFormElement('name', 'Имя');
    $str = '<input id="form-name" name="name" type="text" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута по умолчанию');

    $el->verify('"""\'\'\'@#$%^&');
    $str = '<input id="form-name" name="name" type="text" value="&quot;&quot;&quot;\'\'\'@#$%^&amp;" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с "плохими" символами');
    $el->verify(null);

    $el->setFormName('myform');
    $str = '<input id="form-myform-name" name="myform[name]" type="text" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с именованной формой');

    $el
      ->setUseLabelAsPlaceholder(true)
      ->setLabel('ямИ');
    $str = '<input id="form-myform-name" name="myform[name]" type="text" placeholder="ямИ" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с плейсхолдером из названия поля');

    $el->setPlaceholder('Другое имя');
    $str = '<input id="form-myform-name" name="myform[name]" type="text" placeholder="Другое имя" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с произвольным плейсхолдером');

    $el->setAttribute('myclass');
    $str = '<input id="form-myform-name" name="myform[name]" type="text" class="myclass" placeholder="Другое имя" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с классом и плейсхолдером');

    $el
      ->setPlaceholder(null)
      ->setUseLabelAsPlaceholder(false)
      ->setId('myid')
      ->setName('amen')
      ->setAttribute(array('size'=>10, 'class'=>'yeah'))
      ->addAttribute('maxlength', 20);
    $str = '<input id="form-myform-myid" name="myform[amen]" type="text" size="10" class="yeah" maxlength="20" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с атрибутами');
  }

  function testDefaultValues()
  {
    $el = new TestFormElement('name', 'Имя');
    $el->setFilter('is_numeric');
    $el->setDefault(123);

    $str = '<input id="form-name" name="name" type="text" value="123" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с значением по умолчанию');

    $el->verify('abc');
    $this->assertEquals(true, $el->hasErrors(), 'Есть ошибка по фильтру');
    $str = '<input id="form-name" name="name" type="text" value="abc" />';
    $this->assertEquals($str, $el->renderInput(), 'Инпут с значением по умолчанию и ошибкой в форме');
  }

  function testVerify()
  {
    $el = new TestFormElement('email', 'Email');
    $el->setRegexp(REGULAR_EMAIL);

    $el->verify(null);
    $this->assertEquals(false, $el->hasErrors(), 'По необязательному полю ошибок нет');

    $el->setIsRequired(true);
    $el->verify(null);
    $this->assertEquals(true, $el->hasErrors(), 'По обязательному полю есть ошибки');

    $el->verify('somebody');
    $this->assertEquals(true, $el->hasErrors(), 'По регулярному выражению не проходит');

    $el->verify('my.very@lovely.email.ru');
    $this->assertEquals(false, $el->hasErrors(), 'Корректный email 1');

    $el->verify('my-very@lovely-email.ru');
    $this->assertEquals(false, $el->hasErrors(), 'Корректный email 2');
    $el->setRegexp(null);

    try {
      $el->setFilter('non_callable');
      $this->fail('Фильтр должен выкидывать исключение на невызываемую функцию');
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
      throw $e;
    } catch (Exception $e) {}

    try {
      $el->setFilter('is_numeric');
    } catch (Exception $e) {
      $this->fail('Фильтр не должен выкидывать исключение на корректную функцию');
    }

    $el->verify(123);
    $this->assertEquals(false, $el->hasErrors(), 'Ошибки по фильтру нет');

    $el->verify('abc');
    $this->assertEquals(true, $el->hasErrors(), 'Есть ошибка по фильтру');
  }
}