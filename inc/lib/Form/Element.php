<?php

abstract class FormElement
{
  protected $id;
  protected $form;
  protected $name;
  protected $info;
  protected $label;
  protected $field;
  protected $value;
  protected $regexp;
  protected $filter;
  protected $errors;
  protected $default;
  protected $options;
  protected $attribute;
  protected $form_name;
  protected $is_hidden;
  protected $placeholder;
  protected $is_required;
  protected $ignore_keys;
  protected $label_as_placeholder;

  /** Шаблон для отрисовки поля. is_required, label, input, info, errors */
  protected $tmpl_field = '<tr><th>%s%s:</th><td>%s %s</td></tr>';
  /** Шаблон для отрисовки инпута. Порядок параметров: id, name, attribute, value */
  protected $tmpl_input = '<input id="%s" name="%s" type="text"%s value="%s" />';
  /** Шаблон для доп.информации по полю */
  protected $tmpl_info = '<em>%s</em>';
  /** Шаблон для строки содержащей все строки ошибок */
  protected $tmpl_errs = '<span class="error">%s</span><br />';
  /** Шаблон для одной строки в ошибках */
  protected $tmpl_err = '%s<br />';
  /** Шаблон обязательно поле не заполнено */
  protected $tmpl_err_required = 'Обязательное поле "%s" не заполнено';
  /** Шаблон ошибки по регулярному выражению */
  protected $tmpl_err_regexp = 'Поле "%s" заполнено некорректно';
  /** Шаблон ошибки по фильтру */
  protected $tmpl_err_filter = 'Поле "%s" заполнено некорректно';
  /** Шаблон ошибки при выборе из списка */
  protected $tmpl_err_option = 'Поле "%s" не содержит такого варианта';
  /** Шаблон для обязательного поля */
  protected $tmpl_required = ' <span>*</span> ';

  function __construct($field, $label = null, Form $form = null)
  {
    $this->field = $field;
    $this->name  = $field;
    $this->id    = $field;
    $this->label = $label;
    if ($form) {
      $this->form      = $form;
      $this->form_name = $form->getName();
    }
    $this->init();
  }

  function __toString()
  {
    return $this->render();
  }

  /** Отрисовка блока с полем целиком */
  public function render()
  {
    return sprintf($this->tmpl_field,
      $this->renderIsRequired(),
      $this->getLabel(),
      $this->renderInput(),
      $this->renderInfo(),
      $this->renderErrors()
    );
  }

  /** Отрисовка поля формы */
  public function renderInput()
  {
    return sprintf($this->tmpl_input, $this->getId(), $this->getName(), $this->renderAttribute(), $this->getValue());
  }

  /** Отрисовка ошибок для поля */
  public function renderErrors()
  {
    if (!$this->hasErrors()) {
      return '';
    } else {
      $out = '';
      foreach ($this->errors as $str) {
        $out .= sprintf($this->tmpl_err, $str);
      }
      return sprintf($this->tmpl_errs, $out);
    }
  }

  /** Отрисовка признака обязательности заполнения поля */
  public function renderIsRequired()
  {
    return $this->is_required ? $this->tmpl_required : '';
  }

  /** Отрисовка поля с доп. информацией */
  public function renderInfo()
  {
    return !empty($this->info) ? sprintf($this->tmpl_info, $this->info) : '';
  }

  /** Отрисовка доп.аттрибутов поля */
  public function renderAttribute()
  {
    if ($this->getPlaceholder()) {
      $this->attribute['placeholder'] = $this->getPlaceholder();
    }
    $out = '';
    if (is_array($this->attribute)) {
      foreach ($this->attribute as $key=> $val) {
        $out .= sprintf(' %s="%s"', $key, $val);
      }
    }
    return $out;
  }

  /** Проверка значения поля */
  public function validate($data)
  {
    $this->errors = null;
    $this->value  = $data;
    if (!empty($data)) {
      if ($this->options && !$this->checkValueIsInOptions($data)) {
        $this->errors[] = sprintf($this->tmpl_err_option, $this->label);
      }
      if ($this->regexp && !preg_match($this->regexp, $data)) {
        $this->errors[] = sprintf($this->tmpl_err_regexp, $this->label);
      }
      if ($this->filter && !call_user_func_array($this->filter, array($data))) {
        $this->errors[] = sprintf($this->tmpl_err_filter, $this->label);
      }
    } else {
      if ($this->is_required) {
        $this->errors[] = sprintf($this->tmpl_err_required, $this->label);
      }
    }
    if ($this->hasErrors()) {
      return false;
    }
    return true;
  }

  /** Проверка наличия ошибок после проверки */
  public function hasErrors()
  {
    return is_array($this->errors) && count($this->errors) > 0;
  }

  /**
   * @return array|bool
   */
  public function getErrors()
  {
    return $this->hasErrors() ? $this->errors : false;
  }

  protected function init()
  {

  }

  // GETERS

  /** Значение по умолчанию */
  public function getDefault()
  {
    return $this->default;
  }

  /** Поле в форме */
  public function getField()
  {
    return $this->field;
  }

  /** Имя поля в HTML */
  public function getName()
  {
    return !empty($this->form_name)
      ? $this->form_name . '[' . $this->name . ']'
      : $this->name;
  }

  /** Имя поля для людей при отрисовке */
  public function getLabel()
  {
    return !is_null($this->label) ? $this->label : ucfirst($this->field);
  }

  /** Тег ID для поля */
  public function getId()
  {
    return 'form-' . (!empty($this->form_name) ? $this->form_name . '-' : '') . $this->id;
  }

  /** Тег placeholder для поля */
  public function getPlaceholder()
  {
    return !empty($this->placeholder)
      ? $this->placeholder
      : ($this->label_as_placeholder
        ? $this->label
        : '');
  }

  /**
   * Значение поля формы
   * @param bool $clean - экранировать ли вывод
   */
  public function getValue($clean = true)
  {
    return $clean ? htmlspecialchars($this->value) : $this->value;
  }

  /**
   * Получение название для значения (актуально для SELECTов и CHECKBOXов)
   * Если значение не передано, используется $this->value
   * Если у поля нет опций, просто возвращает значение
   */
  public function getValueName($value = null)
  {
    if (is_null($value)) {
      $value = $this->value;
    }

    if ($this->options) {
      if ($this->checkValueIsInOptions($value)) {
        return $this->ignore_keys ? $value : $this->options[$value];
      } else {
        return false;
      }
    }

    return $value;
  }

  /** Проверка есть ли значение в списке опций */
  protected function checkValueIsInOptions($value)
  {
    return $this->ignore_keys
      ? in_array($value, $this->options)
      : array_key_exists($value, $this->options);
  }

  public function getIsHidden()
  {
    return (bool)$this->is_hidden;
  }

  public function getIsRequired()
  {
    return (bool)$this->is_required;
  }

  /** Форма к которой принадлежит элемент */
  public function getForm()
  {
    return $this->form;
  }

  // SETTERS

  public function setUseLabelAsPlaceholder($val)
  {
    $this->label_as_placeholder = (bool)$val;
    return $this;
  }

  public function setFormName($form_name)
  {
    $this->form_name = $form_name;
    return $this;
  }

  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }

  public function setIsRequired($is_required)
  {
    $this->is_required = $is_required;
    return $this;
  }

  public function setLabel($label)
  {
    $this->label = $label;
    return $this;
  }

  public function setInfo($info)
  {
    $this->info = $info;
    return $this;
  }

  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  public function setPlaceholder($placeholder)
  {
    $this->placeholder = $placeholder;
    return $this;
  }

  public function setRegexp($regexp)
  {
    $this->regexp = $regexp;
    return $this;
  }

  public function setFilter($filter)
  {
    if (!is_callable($filter)) {
      throw new Exception('Фильтр не может быть вызван', 501);
    }
    $this->filter = $filter;
    return $this;
  }

  public function setDefault($default)
  {
    $this->default = $default;
    $this->validate($default);
    return $this;
  }

  public function setAttribute($attribute)
  {
    if (is_string($attribute)) {
      $attribute = array('class'=> $attribute);
    }
    $this->attribute = (array)$attribute;
    return $this;
  }

  public function addAttribute($key, $val)
  {
    $this->attribute[$key] = $val;
    return $this;
  }
}