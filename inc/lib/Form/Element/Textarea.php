<?php

class FormElementTextarea extends FormElement
{
  protected $tmpl_field = '<tr><th colspan="2">%s%s</th></tr><tr><td colspan="2">%s %s %s</td></tr>';
  protected $tmpl_input = '<textarea id="%s" name="%s"%s>%s</textarea>';
}