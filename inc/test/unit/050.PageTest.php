<?php

require_once __DIR__ . '/../init.php';

class PageTest extends PHPUnit_Framework_TestCase
{
  function testRenderTitle()
  {
    $title  = 'Тайтл страницы';
    $header = 'Хедер страницы';

    $p = new Page();
    $p->set('title', $title);

    $this->assertEquals("<title>$title</title>\n", $p->title(), 'Проверяем тайтл');
    $this->assertEquals($title, $p->title(false), 'Проверяем тайтл без тега');

    $this->assertEquals(
      '<h1>' . $title . "</h1>\n", $p->header(), 'По умолчанию заголовок равен тайтлу и находится в H1'
    );
    $this->assertEquals($title, $p->header(false), 'Заголовок можно вывести без тега');

    $p->set('header', $header);
    $this->assertEquals($header, $p->header(false), 'Хедер может быть задан напрямую');
  }

  function testJSandCSS()
  {
    $p = new Page();
    $p->addJS('/some/js.js')
      ->addJS('/another.js')
      ->addCSS('/main.css')
      ->addCSS('/print.css', 'print');

    $css = '<link href="/main.css" rel="stylesheet" type="text/css" />'."\n"
      . '<link href="/print.css" rel="stylesheet" type="text/css" media="print" />'."\n";
    $js  = '<script type="text/javascript" src="/some/js.js"></script>'."\n"
      . '<script type="text/javascript" src="/another.js"></script>'."\n";

    $this->assertEquals($css, $p->css(), 'Сгенерились CSS');
    $this->assertEquals($js, $p->js(), 'Сгенерились JS');
  }

  function testKeywords()
  {
    $p = new Page();
    $keywords = 'купить дешево, быстро и сердито';
    $p->set('keywords', $keywords);
    $html = '<meta name="keywords" content="'.$keywords.'" />'."\n";

    $this->assertEquals($html, $p->keywords(), 'Ключевые слова из строки');

    $arr = array('купить дешево', 'быстро и сердито');
    $p = new Page();
    $p->set('keywords', $arr);
    $this->assertEquals($html, $p->keywords(), 'Ключевые слова из массива');
  }

  function testDescription()
  {
    $p = new Page();
    $descr = 'Описание страницы';
    $p->set('description', $descr);

    $html = '<meta name="description" content="Описание страницы" />'."\n";
    $this->assertEquals($html, $p->description(), 'Поле описание');
  }

  function testMeta()
  {
    $p = new Page();
    $meta = '<!--[if IE 6]>Special instructions for IE 6 here<![endif]-->';
    $p->set('meta', $meta);

    $this->assertEquals($meta, $p->meta(), 'Произвольный HTML для страницы');

    if (!defined('SITE_URL')) { //Тег canonical использует эту константу
      define('SITE_URL', 'www.cmsx.ru');
    }
    $canonical = '/some/page';
    $html = '<link rel="canonical" href="http://'.SITE_URL.'/some/page" />'."\n";
    $p->set('canonical', $canonical);
    $this->assertEquals($html, $p->canonical(), 'META тег rel="canonical"');
  }
}