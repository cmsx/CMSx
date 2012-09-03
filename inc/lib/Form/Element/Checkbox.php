<?php

class FormElementCheckbox extends FormElement
{
  protected $tmpl_input = '<input id="%s" name="%s" type="checkbox"%s value="%s" />';
  protected $checkbox_value = 1;

  public function renderInput()
  {
    if ($this->value == $this->checkbox_value) {
      $this->attribute['checked'] = 'checked';
    }
    return sprintf($this->tmpl_input, $this->getId(), $this->getName(), $this->renderAttribute(), $this->checkbox_value);
  }

  /** Возвращает value чекбокса или false */
  public function getValue($clean = true)
  {
    return $this->getIsChecked() ? $this->checkbox_value : false;
  }

  /** Возвращает bool установлена ли галочка */
  public function getIsChecked()
  {
    return $this->value == $this->checkbox_value;
  }

  /** Установка аттрибута value для чекбокса, по умолчанию = 1 */
  public function setValue($val)
  {
    $this->checkbox_value = $val;
    return $this;
  }
}