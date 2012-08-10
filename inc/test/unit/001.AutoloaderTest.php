<?php

define('NO_CONFIG', true);
require_once __DIR__.'/../../init.php';

class AutoloaderTest extends PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider classnames
   */
  function testPathExplode($class, $expected)
  {
    $arr = Autoloader::FindParts($class);
    $this->assertEquals($expected, $arr, $class.' to '.join('/', $expected));
  }

  function testLoadClass()
  {
    try {
      new SQL('pages');
      new BaseSchema();
    } catch ( Exception $e ) {
      $this->fail($e->getMessage());
    }
  }

  function classnames()
  {
    return array(
      array('simple', array('simple')),
      array('SIMPLE', array('SIMPLE')),
      array('TestMe', array('Test', 'Me')),
      array('testMe', array('test', 'Me')),
      array('testMeMore', array('test', 'Me', 'More')),
      array('APIMaybe', array('API', 'Maybe')),
      array('APIMaybeDificult', array('API', 'Maybe', 'Dificult')),
      array('APIByHTML', array('API', 'By', 'HTML')),
      array('Zend_StyleNOMATTERExploding_Yeah', array('Zend','StyleNOMATTERExploding','Yeah')),
    );
  }
}