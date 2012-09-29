<?php

require_once __DIR__ . '/../init.php';

class TemplateTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    Template::SetTemplatesDir(DIR_TEST.'/resourse');
  }

  public static function tearDownAfterClass()
  {
    Template::SetTemplatesDir(DIR_TMPL);
  }

  function testRender()
  {
    $t = new Template('template.php');
    $t->set('name', 'John')
      ->set('city', 'Moscow');
    $exp = '<h1>John</h1> Moscow';
    $this->assertEquals($exp, $t->render(), 'Рендер шаблона');
  }
}