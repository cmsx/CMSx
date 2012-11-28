<?php

require_once __DIR__.'/../init.php';

class URLTest extends PHPUnit_Framework_TestCase
{
  function testParsingStrings () {
    $exp = array( array(1=>'test', 'me'), array() );
    $act = URL::Parse('/test/me/');
    $this->assertEquals( $exp, $act, 'Two' );

    $exp = array( array(1=>'test', 'me'), array('id'=>12, 'some'=>'thing') );
    $act = URL::Parse('/test/me/id:12/some:thing/');
    $this->assertEquals( $exp, $act, 'Two & two param' );

    $exp = array( array(1=>'test', 'me'), array('id'=>array(12,13)) );
    $act = URL::Parse('/test/me/id:12/id:13/');
    $this->assertEquals( $exp, $act, 'Two & one multi param' );

    $exp = array( array(1=>'русский', 'язык'), array() );
    $act = URL::Parse('/русский/язык/');
    $this->assertEquals( $exp, $act, 'Russian lang' );

    $exp = array( array(1=>'test', 'me', '#some'), array() );
    $act = URL::Parse('/test/me/#some');
    $this->assertEquals( $exp, $act, 'URL with #anchor' );

    $exp = array( array(1=>'test', 'me', 'file.txt'), array() );
    $act = URL::Parse('/test/me/file.txt');
    $this->assertEquals( $exp, $act, 'URL with file and extension' );
  }

  /**
   * @dataProvider builddata
   */
  function testBuilding($args, $params, $exp, $msg = null)
  {
    $this->assertEquals($exp, Url::Build($args, $params), $msg);
  }

  function testAddingParams()
  {
    $exp = '/hello/name:John/id:12/';
    $u = new URL;
    $u->addParameter('name', 'John')
      ->addArgument('hello')
      ->addParameter('id', 12);
    $this->assertEquals($exp, $u->toString(), 'Добавление параметров к пустому URL');

    $exp = '/hello/world/';
    $u = new URL('/hello/world/id:12/');
    $u->setParameter('id');
    $this->assertEquals($exp, $u->toString(), 'Удаление параметра');

    $exp = '/hello/world/id:12/id:15/';
    $u = new URL('/hello/world/id:12/');
    $u->addParameter('id', 15);
    $this->assertEquals($exp, $u->toString(), 'Добавление параметра к существующему');
  }

  function testUrlToString()
  {
    $u = new URL();
    $u->addArgument('hello')
      ->addParameter('id', 12);

    $exp = '/hello/id:12/';
    $this->assertEquals($exp, $u->toString(), 'Преобразование URL в строку');

    $exp = '<a class="hello" href="/hello/id:12/">Привет</a>';
    $this->assertEquals($exp, $u->toHTML('Привет', 'hello'), 'Преобразование URL в ссылку');
  }

  function builddata()
  {
    return array(
      array(
        array('test', 'me'),
        null,
        '/test/me/',
        'Без параметров'
      ),

      array(
        array('test', 'me'),
        array('id' => 12, 'hello' => 'world'),
        '/test/me/id:12/hello:world/',
        'Простые параметры'
      ),

      array(
        array('test', 'me'),
        array('id' => array(12, 15, 16), 'hello' => 'world'),
        '/test/me/id:12/id:15/id:16/hello:world/',
        'Сложные параметры'
      ),
    );
  }
}