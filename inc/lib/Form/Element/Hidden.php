<?php

class FormElementHidden extends FormElement
{
  protected $is_hidden = true;
  protected $tmpl_input = '<input id="%s" name="%s" type="hidden"%s value="%s" />';

  public function render()
  {
    return $this->renderInput();
  }
}