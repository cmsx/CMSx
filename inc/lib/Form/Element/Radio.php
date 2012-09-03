<?php

class FormElementRadio extends FormElement
{
  protected $tmpl_input = '<label><input id="%s" name="%s" type="radio"%s value="%s" /> %s</label>';

  /** Установка вариантов для выбора */
  public function setOptions($options, $_ = null)
  {
    if (is_array($options) || is_null($options)) {
      $this->options = $options;
    } else {
      $this->options = func_get_args();
    }
    return $this;
  }

  /** Использовать значения массива опций как значения поля */
  public function setIgnoreKeys($on)
  {
    $this->ignore_keys = $on;
    return $this;
  }

  public function renderInput()
  {
    if (!$this->options) {
      return;
    }

    $out = '';
    foreach ($this->options as $key=>$value) {
      $attr = '';
      $val = $key;
      if ($this->ignore_keys) {
        $val = $value;
      }
      if ($this->value == $val) {
        $attr = ' checked="checked"';
      }
      $out .= sprintf($this->tmpl_input, $this->getId(), $this->getName(), $attr, $val, $value);
    }

    return $out;
  }

}