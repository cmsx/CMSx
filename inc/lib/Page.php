<?php

class Page extends Template
{
  protected $layout;
  protected $doctype = self::DOCTYPE_HTML_4_TRANSITIONAL;

  protected static $default_layout;
  protected static $default_template;
  protected static $doctype_arr = array(
    self::DOCTYPE_HTML_5              => '<!DOCTYPE html>',
    self::DOCTYPE_HTML_4_STRICT       => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
    self::DOCTYPE_HTML_4_TRANSITIONAL => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
    self::DOCTYPE_XHTML_STRICT        => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
    self::DOCTYPE_XHTML_TRANSITIONAL  => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
  );

  const DOCTYPE_HTML_4_STRICT       = 'html4strict';
  const DOCTYPE_HTML_4_TRANSITIONAL = 'html4transitional';
  const DOCTYPE_HTML_5              = 'html5';
  const DOCTYPE_XHTML_STRICT        = 'xhtml_strict';
  const DOCTYPE_XHTML_TRANSITIONAL  = 'xhtml_transitional';

  /** Отрисовка страницы */
  public function render()
  {
    return parent::render($this->layout ?: self::$default_layout);
  }

  /** Установка Layout */
  public function setLayout($layout)
  {
    $this->layout = $layout;
    return $this;
  }

  /** Установка DOCTYPE из self::DOCTYPE_* */
  public function setDoctype($doctype)
  {
    $this->doctype = $doctype;

    return $this;
  }

  /**
   * Заголовок в теге TITLE
   * @param $tag вывести значение в теге или отдельно
   */
  public function title($tag = 'title')
  {
    if (!$t = $this->get('title')) {
      return false;
    }
    return $tag ? sprintf('<%s>%s</%1$s>'."\n", $tag, $t) : $t;
  }

  /**
   * Заголовок на странице.
   * @param $tag вывести значение в теге или отдельно
   */
  public function header($tag = 'h1')
  {
    $header = $this->get('header') ?: $this->get('title');
    return $tag ? sprintf('<%s>%s</%1$s>'."\n", $tag, $header) : $header;
  }

  /** Текст страницы */
  public function text()
  {
    return $this->get('text');
  }

  /** META-тег ключевые слова */
  public function keywords()
  {
    if (!$k = $this->get('keywords')) {
      return false;
    }
    if (is_array($k)) {
      $k = join(', ', $k);
    }
    return '<meta name="keywords" content="'.$k.'" />'."\n";
  }

  /** META-тег описание */
  public function description()
  {
    if (!$d = $this->get('description')) {
      return false;
    }
    return '<meta name="description" content="'.$d.'" />'."\n";
  }

  /** Отображение произвольных МЕТА тегов для страницы */
  public function meta()
  {
    return $this->get('meta');
  }

  /** Основной шаблон страницы */
  public function body()
  {
    return parent::render($this->template ?: self::$default_template);
  }

  /** Отображение тега link rel="canonical" */
  public function canonical()
  {
    if (!$c = $this->get('canonical')) {
      return false;
    }
    return sprintf('<link rel="canonical" href="http://%s%s" />', SITE_URL, $c)."\n";
  }

  /** Отображение подключаемых CSS тегов */
  public function css()
  {
    $css = $this->get('css');
    if (!is_array($css) || !count($css)) {
      return false;
    }
    $out = '';
    foreach ($css as $file) {
      $out .= sprintf(
        '<link href="%s" rel="stylesheet" type="text/css"%s />',
        $file['file'],
        ($file['media'] ? ' media="' . $file['media'] . '"' : '')
      ) . "\n";
    }
    return $out;
  }

  /** Отображение подключаемых JS тегов */
  public function js()
  {
    $js = $this->get('js');
    if (!is_array($js) || !count($js)) {
      return false;
    }
    $out = '';
    foreach ($js as $file) {
      $out .= '<script type="text/javascript" src="'.$file['file'].'"></script>'."\n";
    }
    return $out;
  }

  /** Отрисовка DOCTYPE */
  public function doctype()
  {
    return self::$doctype_arr[$this->doctype]."\n";
  }

  /** Отрисовка тега META charset */
  public function charset($charset = 'utf-8')
  {
    return ( $this->doctype == self::DOCTYPE_HTML_5
      ? '<meta charset="'.$charset.'" />'
      : '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />' )."\n";
  }

  /** Отрисовка тега HTML */
  public function html()
  {
    return ( $this->doctype == self::DOCTYPE_XHTML_STRICT || $this->doctype == self::DOCTYPE_XHTML_TRANSITIONAL
      ? '<html xmlns="http://www.w3.org/1999/xhtml">'
      : '<html>' )."\n";
  }

  /** Добавление CSS к шаблону страницы */
  public function addCSS($file, $media = null)
  {
    if (!$this->get('css')) {
      $this->set('css', array());
    }
    $arr = array(array('file' => $file, 'media' => $media));
    $this->append('css', $arr);
    return $this;
  }

  /** Добавление JS к шаблону страницы */
  public function addJS($file)
  {
    if (!$this->get('js')) {
      $this->set('js', array());
    }
    $arr = array(array('file' => $file));
    $this->append('js', $arr);
    return $this;
  }

  /** Layout по-умолчанию */
  public static function SetDefaultLayout($layout)
  {
    self::$default_layout = $layout;
  }

  /** Шаблон по-умолчанию */
  public static function SetDefaultTemplate($template)
  {
    self::$default_template = $template;
  }
}