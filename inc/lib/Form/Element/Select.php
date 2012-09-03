<?php

class FormElementSelect extends FormElement
{
  protected $tmpl_input = '<select id="%s" name="%s">%s</select>';
  protected $tmpl_option = '<option value="%s"%s>%s</option>';

  protected $label_as_placeholder = true;

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
    $out = '<option value="">' . $this->getPlaceholder() . '</option>';
    if ($this->options) {
      foreach ($this->options as $key => $value) {
        $val = $key;
        if ($this->ignore_keys) {
          $val = $value;
        }
        $checked = ($val == $this->value ? ' selected="selected"' : '');
        $out .= sprintf($this->tmpl_option, $val, $checked, $value);
      }
    }
    return sprintf($this->tmpl_input, $this->getId(), $this->getName(), $out);
  }
}