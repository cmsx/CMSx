<?php

require_once __DIR__.'/../init.php';

class FormElementTest extends PHPUnit_Framework_TestCase
{
  function testConstruct()
  {
    $form = new Form('myform');

    $el = new FormElementInput('name', 'Имя', $form);
    $this->assertEquals('name', $el->getField(), 'Имя поля');
    $this->assertEquals('Имя', $el->getLabel(), 'Название поля');
    $this->assertEquals('myform[name]', $el->getName(), 'Тег name');
    $this->assertEquals('form-myform-name', $el->getId(), 'Тег id');
  }

  function testRender()
  {
    $el  = new FormElementInput('name', 'Имя');
    $str = '<input id="form-name" name="name" type="text" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута по умолчанию');

    $el->validate('"""\'\'\'@#$%^&');
    $str = '<input id="form-name" name="name" type="text" value="&quot;&quot;&quot;\'\'\'@#$%^&amp;" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с "плохими" символами');
    $el->validate(null);

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
      ->setAttribute(array('size'=> 10, 'class'=> 'yeah'))
      ->addAttribute('maxlength', 20);
    $str = '<input id="form-myform-myid" name="myform[amen]" type="text" size="10" class="yeah" maxlength="20" value="" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с атрибутами');
  }

  function testDefaultValues()
  {
    $el = new FormElementInput('name', 'Имя');
    $el->setFilter('is_numeric');
    $el->setDefault(123);

    $str = '<input id="form-name" name="name" type="text" value="123" />';
    $this->assertEquals($str, $el->renderInput(), 'Создание инпута с значением по умолчанию');

    $el->validate('abc');
    $this->assertEquals(true, $el->hasErrors(), 'Есть ошибка по фильтру');
    $str = '<input id="form-name" name="name" type="text" value="abc" />';
    $this->assertEquals($str, $el->renderInput(), 'Инпут с значением по умолчанию и ошибкой в форме');
  }

  function testVerify()
  {
    $el = new FormElementInput('email', 'Email');
    $el->setRegexp(REGULAR_EMAIL);

    $el->validate(null);
    $this->assertEquals(false, $el->hasErrors(), 'По необязательному полю ошибок нет');

    $el->setIsRequired(true);
    $el->validate(null);
    $this->assertEquals(true, $el->hasErrors(), 'По обязательному полю есть ошибки');

    $el->validate('somebody');
    $this->assertEquals(true, $el->hasErrors(), 'По регулярному выражению не проходит');

    $el->validate('my.very@lovely.email.ru');
    $this->assertEquals(false, $el->hasErrors(), 'Корректный email 1');

    $el->validate('my-very@lovely-email.ru');
    $this->assertEquals(false, $el->hasErrors(), 'Корректный email 2');
    $el->setRegexp(null);

    try {
      $el->setFilter('non_callable');
      $this->fail('Фильтр должен выкидывать исключение на невызываемую функцию');
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
      throw $e;
    } catch (Exception $e) {
    }

    try {
      $el->setFilter('is_numeric');
    } catch (Exception $e) {
      $this->fail('Фильтр не должен выкидывать исключение на корректную функцию');
    }

    $el->validate(123);
    $this->assertEquals(false, $el->hasErrors(), 'Ошибки по фильтру нет');

    $el->validate('abc');
    $this->assertEquals(true, $el->hasErrors(), 'Есть ошибка по фильтру');
  }

  function testHidden()
  {
    $hid = new FormElementHidden('hi');
    $hid->setDefault(123);
    $exp = '<input id="form-hi" name="hi" type="hidden" value="123" />';
    $this->assertEquals($exp, $hid->renderInput(), 'Input hidden');
  }

  function testRadio()
  {
    $radio = new FormElementRadio('name');
    $radio->setOptions('one', 'two', 'three');
    $exp = '<label><input id="form-name" name="name" type="radio" checked="checked" value="0" /> one</label>'
      . '<label><input id="form-name" name="name" type="radio" value="1" /> two</label>'
      . '<label><input id="form-name" name="name" type="radio" value="2" /> three</label>';
    $this->assertEquals($exp, $radio->renderInput(), 'Радио кнопки по списку вариантов');

    $radio->setIgnoreKeys(true);
    $exp = '<label><input id="form-name" name="name" type="radio" value="one" /> one</label>'
      . '<label><input id="form-name" name="name" type="radio" value="two" /> two</label>'
      . '<label><input id="form-name" name="name" type="radio" value="three" /> three</label>';
    $this->assertEquals($exp, $radio->renderInput(), 'Радио кнопки по списку вариантов без ключей');

    $radio = new FormElementRadio('name');
    $radio->setOptions(array('one'=> 'Один', 'two'=> 'Два'));
    $radio->validate('two');
    $exp = '<label><input id="form-name" name="name" type="radio" value="one" /> Один</label>'
      . '<label><input id="form-name" name="name" type="radio" checked="checked" value="two" /> Два</label>';
    $this->assertEquals($exp, $radio->renderInput(), 'Радио кнопки по массиву');
  }

  function testCheckbox()
  {
    $ch  = new FormElementCheckbox('city');
    $exp = '<input id="form-city" name="city" type="checkbox" value="1" />';
    $this->assertEquals($exp, $ch->renderInput(), 'Простой чекбокс');

    $ch->validate('some another value');
    $ch->setAttribute('myclass');
    $exp = '<input id="form-city" name="city" type="checkbox" class="myclass" value="1" />';
    $this->assertEquals($exp, $ch->renderInput(), 'Валидация неверного значения + задаем атрибуты');
    $this->assertEquals(0, $ch->getValue(), 'Значение выключенного чекбокса');

    $ch->validate(true);
    $exp = '<input id="form-city" name="city" type="checkbox" class="myclass" checked="checked" value="1" />';
    $this->assertEquals($exp, $ch->renderInput(), 'Валидация чекбокса с правильным значением');
    $this->assertEquals(1, $ch->getValue(), 'Значение включенного чекбокса');

    $ch = new FormElementCheckbox('value');
    $ch->setValue('something');
    $ch->validate('another');
    $exp = '<input id="form-value" name="value" type="checkbox" value="something" />';
    $this->assertEquals($exp, $ch->renderInput(), 'Чекбокс с произвольным значением');
    $this->assertFalse($ch->getIsChecked(), 'С неверным значением чекбокс не отмечен');
    $this->assertFalse($ch->getValue(), 'Значение не отмеченного чекбокса false');

    $ch->validate('something');
    $this->assertTrue($ch->getIsChecked(), 'Правильное значение - чекбокс отмечен');
    $this->assertEquals('something', $ch->getValue(), 'Значение заданного значения');
  }

  function testSelect()
  {
    $sel = new FormElementSelect('gender', 'Пол');
    $sel->setOptions('male', 'female');

    $exp = '<select id="form-gender" name="gender">'
      . '<option value="">Пол</option>'
      . '<option value="0" selected="selected">male</option>'
      . '<option value="1">female</option></select>';
    $this->assertEquals($exp, $sel->renderInput(), 'Селект по списку значений');

    $sel = new FormElementSelect('gender', 'Пол');
    $sel->setOptions(array('m'=> 'male', 'f'=> 'female'));
    $exp = '<select id="form-gender" name="gender">'
      . '<option value="">Пол</option>'
      . '<option value="m">male</option>'
      . '<option value="f">female</option></select>';
    $this->assertEquals($exp, $sel->renderInput(), 'Селект по массиву');

    $sel->setIgnoreKeys(true);
    $exp = '<select id="form-gender" name="gender">'
      . '<option value="">Пол</option>'
      . '<option value="male">male</option>'
      . '<option value="female">female</option></select>';
    $this->assertEquals($exp, $sel->renderInput(), 'Селект по списку значений без ключей');

    $sel->setUseLabelAsPlaceholder(false);
    $exp = '<select id="form-gender" name="gender">'
      . '<option value=""></option>'
      . '<option value="male">male</option>'
      . '<option value="female">female</option></select>';
    $this->assertEquals($exp, $sel->renderInput(), 'Селект по списку значений без ключей');

    $sel->setPlaceholder('Нужно выбрать');
    $exp = '<select id="form-gender" name="gender">'
      . '<option value="">Нужно выбрать</option>'
      . '<option value="male">male</option>'
      . '<option value="female">female</option></select>';
    $this->assertEquals($exp, $sel->renderInput(), 'Селект по списку значений без ключей');
  }
  
  function testTextarea()
  {
    $ta = new FormElementTextarea('text');
    $exp = '<textarea id="form-text" name="text"></textarea>';
    $this->assertEquals($exp, $ta->renderInput(), 'Обычное текстовое поле');

    $ta->setAttribute(array('rows'=>5, 'cols'=>12));
    $ta->validate('some data');
    $exp = '<textarea id="form-text" name="text" rows="5" cols="12">some data</textarea>';
    $this->assertEquals($exp, $ta->renderInput(), 'Поле с данными');

    $ta->setAttribute(array());
    $ta->validate('&lt;tag&gt;');
    $exp = '<textarea id="form-text" name="text">&amp;lt;tag&amp;gt;</textarea>';
    $this->assertEquals($exp, $ta->renderInput(), 'В поле подставлены подлые символы');
  }
}