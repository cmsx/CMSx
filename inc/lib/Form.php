<?php

class Form
{
  protected $name;
  protected $data;
  protected $fields;
  protected $action;
  protected $errors;
  protected $values;
  protected $submit_button = 'Отправить';

  protected $tmpl_submit = '<div class="submit">%s</div>';
  protected $tmpl_submit_button = '<button type="submit">%s</button>';

  function __construct($name = null)
  {
    $this->name = $name;
    $this->init();
  }

  public function getData($field = null)
  {
    if (is_null($field)) {
      return $this->data ? : false;
    }
    return isset($this->data[$field]) ? $this->data[$field] : false;
  }

  /** Инициализация для наследников */
  protected function init() {}

  /** Отрисовка формы целиком */
  public function render()
  {
    $str = "<!-- Form -->\n<form action=\"%s\"%s method=\"POST\">\n"
          ."<!-- Fields -->\n%s<!-- /Fields -->\n%s</form>\n<!-- /Form -->\n";
    return sprintf(
      $str,
      $this->action,
      !empty($this->name) ? ' id="form-'.$this->name.'"' : '',
      $this->renderFields(),
      $this->renderSubmit()
    );
  }

  /**
   * Отрисовка полей формы
   * @param $is_hidden: null|false|true отрисовывать поля: все подряд, только видимые, только скрытые
   */
  public function renderFields($is_hidden = null)
  {
    $out = '';
    /** @var $element FormElement */
    foreach ($this->fields as $element) {
      if (is_null($is_hidden) || $element->getIsHidden() == $is_hidden) {
        $out .= $element->render()."\n";
      }
    }
    return $out;
  }

  /** Отрисовка блока отправки формы */
  public function renderSubmit($submit = null)
  {
    return sprintf($this->tmpl_submit, $this->renderSubmitButton($submit))."\n";
  }

  /** Отрисовка кнопки отправки формы */
  public function renderSubmitButton($submit = null)
  {
    $submit = is_null($submit) ? $this->submit_button : $submit;
    return sprintf($this->tmpl_submit_button, $submit);
  }

  /** Проверка данных, отправленных пользователем */
  public function verify($data = null)
  {
    if (is_null($data)) {
      $data = !empty($this->name) ? $_POST[$this->name] : $_POST;
    }
    $this->errors = null;
    $this->verifyFields($data);
    if ( !$this->hasErrors() ) {
      $this->data = $this->values;
      return true;
    } else {
      return false;
    }
  }

  /** Проверить все поля формы */
  protected function verifyFields($data)
  {
    /** @var $element FormElement */
    foreach ($this->fields as $field=> $element) {
      $val = isset ($data[$field]) ? $data[$field] : null;
      if ($element->verify($val)) {
        $this->values[$field] = $element->getValue(false);
      } else {
        $this->values[$field] = $val;
        $this->errors[$field] = $element->getErrors();
      }
    }
  }

  /** Есть ли ошибки после верификации */
  public function hasErrors()
  {
    return is_array($this->errors) && count($this->errors) > 0;
  }

  /**
   * @param $plain - вернуть в виде массива field=>array() или текстом
   * @return array|bool
   */
  public function getErrors($plain = false)
  {
    if (!$this->hasErrors()) {
      return false;
    }
    if ($plain) {
      $out = '';
      foreach ($this->errors as $arr) {
        $out .= join("\n", $arr)."\n";
      }
      return $out;
    } else {
      return $this->errors;
    }
  }

  /** @return FormElement|bool */
  public function field($field)
  {
    return isset($this->fields[$field]) ? $this->fields[$field] : false;
  }

  /**
   * Добавить в форму поле ввода INPUT
   * @return FormElementInput
   */
  public function addInput($field, $label = null)
  {
    return $this->fields[$field] = new FormElementInput($field, $label, $this->name);
  }

  /** Значения формы по умолчанию */
  public function setDefaultValues($values)
  {
    $this->values = $values;
    return $this;
  }
}