<?php

class Page extends Template
{
  protected $layout;
  protected static $default_layout;
  protected static $default_template;

  public function render()
  {
    return parent::render($this->layout ?: self::$default_layout);
  }

  public function setLayout($layout)
  {
    $this->layout = $layout;
    return $this;
  }

  public function title()
  {
    return $this->get('title');
  }

  public function header()
  {
    return $this->get('header');
  }

  public function text()
  {
    return $this->get('text');
  }

  public function keywords()
  {
    return $this->get('keywords');
  }

  public function description()
  {
    return $this->get('description');
  }

  public function meta()
  {
    return $this->get('meta');
  }

  public function body()
  {
    return parent::render($this->template ?: self::$default_template);
  }

  public function canonical()
  {
    return $this->get('canonical');
  }

  public function css()
  {
    return $this->get('css');
  }

  public function js()
  {
    return $this->get('js');
  }

  public static function SetDefaultLayout($layout)
  {
    self::$default_layout = $layout;
  }

  public static function SetDefaultTemplate($template)
  {
    self::$default_template = $template;
  }
}