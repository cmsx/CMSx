<?php

class Template extends Standart
{
  protected $template;
  protected static $dir;

  function __construct($template = null, $vars = null)
  {
    $this->setTemplate($template);
    $this->vars = $vars;
  }

  function __toString()
  {
    return $this->render();
  }

  public function setTemplate($template)
  {
    if (!empty($template)) {
      $this->template = $template;
    }
    return $this;
  }

  public function render($template = null)
  {
    if (is_null($template)) {
      $template = $this->template;
    }
    if ($template) {
      ob_start();
      extract($this->vars,EXTR_OVERWRITE);
      include self::$dir.DIRECTORY_SEPARATOR.$template;
      return ob_get_clean();
    } else {
      return 'Шаблон не задан'.(DEVMODE ? ':<br />'.nl2br(print_r($this->vars, true)) : '');
    }
  }

  public static function SetTemplatesDir($dir)
  {
    self::$dir = $dir;
  }
}