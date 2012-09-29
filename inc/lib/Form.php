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

  protected $tmpl_table = '<table>';
  protected $tmpl_submit = '<tr class="submit"><td colspan=2>%s</td></div>';
  protected $tmpl_submit_button = '<button type="submit">%s</button>';
  protected $tmpl_layout = "<!-- Form -->\n<form action=\"%s\"%s method=\"POST\">\n%s
  <!-- Fields -->\n%s\n%s\n%s<!-- /Fields -->\n%s</table></form>\n<!-- /Form -->\n";

  function __construct($name = null)
  {
    $this->name = $name;
    $this->init();
  }

  function __toString()
  {
    return $this->render();
  }

  public function getData($field = null)
  {
    if (is_null($field)) {
      return $this->data ? : false;
    }
    return isset($this->data[$field]) ? $this->data[$field] : false;
  }

  public function getName()
  {
    return $this->name;
  }

  /** Инициализация для наследников */
  protected function init()
  {
  }

  /** Процессинг формы */
  protected function process()
  {
    if ($this->isValid()) {
      //It seems like something has to be here :)
    }
  }

  /** Отрисовка формы целиком */
  public function render()
  {
    return sprintf(
      $this->tmpl_layout,
      $this->action,
      !empty($this->name) ? ' id="form-' . $this->name . '"' : '',
      $this->renderErrors(),
      $this->renderFields(true),
      $this->tmpl_table,
      $this->renderFields(false),
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
        $out .= $element->render() . "\n";
      }
    }
    return $out;
  }

  public function renderErrors()
  {
    return nl2br($this->getErrors(true));
  }

  /** Отрисовка блока отправки формы */
  public function renderSubmit($submit = null)
  {
    return sprintf($this->tmpl_submit, $this->renderSubmitButton($submit)) . "\n";
  }

  /** Отрисовка кнопки отправки формы */
  public function renderSubmitButton($submit = null)
  {
    $submit = is_null($submit) ? $this->submit_button : $submit;
    return sprintf($this->tmpl_submit_button, $submit);
  }

  /** Проверка данных, отправленных пользователем */
  public function validate($data = null)
  {
    if (is_null($data)) {
      if (!$this->isSent()) {
        return false;
      }
      $data = !empty($this->name) ? $_POST[$this->name] : $_POST;
    }
    $this->errors = null;
    $this->verifyFields($data);
    if (!$this->hasErrors()) {
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
      if ($element->validate($val)) {
        $this->values[$field] = $element->getValue(false);
      } else {
        $this->values[$field] = $val;
        $this->errors[$field] = $element->getErrors();
      }
    }
  }

  /** Есть ли ошибки */
  public function hasErrors()
  {
    return is_array($this->errors) && count($this->errors) > 0;
  }

  /** Проверка, отправлена ли форма */
  public function isSent()
  {
    return $this->name ? isset($_POST[$this->name]) : count($_POST) > 0;
  }

  /** Проверка была ли форма отправлена и проверена */
  public function isValid()
  {
    return $this->isSent() && !$this->hasErrors();
  }

  /**
   * @param $plain - вернуть в виде массива field=>array() или текстом
   *
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
        $out .= join("\n", $arr) . "\n";
      }
      return $out;
    } else {
      return $this->errors;
    }
  }

  /** Значения формы по умолчанию */
  public function setDefaultValues($values)
  {
    $this->values = $values;
    return $this;
  }

  /** Действие для формы */
  public function setAction($action)
  {
    $this->action = $action;
    return $this;
  }

  /** Установка текста для кнопки отправки */
  public function setSubmitButton($submit)
  {
    $this->submit_button = $submit;
    return $this;
  }

  /** @return FormElement|bool */
  public function field($field)
  {
    return isset($this->fields[$field]) ? $this->fields[$field] : false;
  }

  /**
   * Добавить в форму поле ввода INPUT
   *
   * @return FormElementInput
   */
  public function addInput($field, $label = null)
  {
    return $this->fields[$field] = new FormElementInput($field, $label, $this);
  }

  /**
   * Добавить в форму поле ввода SELECT
   *
   * @return FormElementSelect
   */
  public function addSelect($field, $label)
  {
    return $this->fields[$field] = new FormElementSelect($field, $label, $this);
  }

  /**
   * Добавить в форму поле ввода RADIO
   *
   * @return FormElementRadio
   */
  public function addRadio($field, $label)
  {
    return $this->fields[$field] = new FormElementRadio($field, $label, $this);
  }

  /**
   * Добавить в форму поле ввода CHECKBOX
   *
   * @return FormElementCheckbox
   */
  public function addCheckbox($field, $label)
  {
    return $this->fields[$field] = new FormElementCheckbox($field, $label, $this);
  }

  /**
   * Добавить в форму поле ввода HIDDEN
   *
   * @return FormElementHidden
   */
  public function addHidden($field, $label)
  {
    return $this->fields[$field] = new FormElementHidden($field, $label, $this);
  }

  /**
   * Добавить в форму поле Textarea
   *
   * @return FormElementTextarea
   */
  public function addText($field = 'text', $label = 'Текст')
  {
    return $this->fields[$field] = new FormElementTextarea($field, $label, $this);
  }
}