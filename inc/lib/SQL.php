<?php

class SQL
{
  protected $table;
  protected $columns;
  protected $action;
  protected $where;
  protected $limit;
  protected $offset;
  protected $groupby;
  protected $orderby;
  protected $values;
  protected $binded_values;
  protected $join_arr;
  protected $prefix;
  protected $no_bind;

  static protected $default_prefix;
  static protected $pdo_bind = true;

  function __construct($table, $prefix = NULL)
  {
    $this->prefix = is_null($prefix) ? self::$default_prefix : $prefix;
    $this->action('select')->table($table);
  }

  function __toString()
  {
    return $this->toString();
  }

  function toString()
  {
    $nb = $this->no_bind;
    $this->setNoBind(true);
    $sql = $this->make();
    $this->setNoBind($nb);
    return $sql;
  }

  /** ACTIONS **/
  public function make()
  {
    $sql = $this->action.' ';
    $obj = !$this->no_bind && self::$pdo_bind ? $this : NULL;
    switch ($this->action) {
      case 'SELECT':
        $sql .= ($this->columns ? self::ColumnsToSQL($this->columns) : '*')
          .' FROM '.self::ColumnsToSQL($this->table, $this->prefix);
        if (is_array($this->join_arr)) {
          foreach ($this->join_arr as $arr)
            $sql .= self::BuildJoin($arr['table'], $arr['on'], $arr['type']);
        }
        $sql .= self::BuildWhere($this->where, $obj)
          .self::BuildGroupBy($this->groupby)
          .self::BuildOrderBy($this->orderby)
          .self::BuildLimit($this->limit, $this->offset);
        break;
      case 'UPDATE':
        $sql .= self::ColumnsToSQL($this->table, $this->prefix)
          .' SET '.self::ValuesToSQL($this->values, $obj)
          .self::BuildWhere($this->where, $obj)
          .self::BuildLimit($this->limit, $this->offset);
        break;
      case 'DELETE':
        $sql .= 'FROM '.self::ColumnsToSQL($this->table, $this->prefix)
          .self::BuildWhere($this->where, $obj)
          .self::BuildLimit($this->limit, $this->offset);
        break;
      case 'INSERT':
        if (!$this->no_bind && self::$pdo_bind) {
          $vals = join(', ', array_keys($this->binded_values));
        } else {
          $tmp = array();
          foreach ($this->values as $v) $tmp[] = self::QuoteValue($v);
          $vals = join(', ', $tmp);
        }

        $sql .= 'INTO '.self::ColumnsToSQL($this->table, $this->prefix)
          .' ('.self::ColumnsToSQL(array_keys($this->values)).')'
          .' VALUES ('.$vals.')';
        break;
    }
    return $sql;
  }

  public function select()
  {
    return $this->make();
  }

  public function update($values)
  {
    return $this->values($values)->action('update')->make();
  }

  public function delete()
  {
    return $this->action('delete')->action('delete')->make();
  }

  public function insert($values)
  {
    return $this->action('insert')->values($values)->make();
  }

  public function bindValue($key, $val)
  {
    $this->binded_values[':'.$key] = $val;
    return $this;
  }

  public function getBindedValue($key)
  {
    return isset ($this->binded_values[$key]) ? $this->binded_values[$key] : NULL;
  }

  public function getBindedValues()
  {
    return !$this->no_bind && count($this->binded_values) ? $this->binded_values : NULL;
  }

  public function getAction()
  {
    return $this->action;
  }

  /** SETTERS **/
  public function columns($mixed)
  {
    if (!is_null($mixed)) {
      $this->columns = func_get_args();
    }
    return $this;
  }

  public function limit($limit, $offset = NULL)
  {
    $this->limit = $limit;
    if (!is_null($offset)) {
      $this->offset = $offset;
    }
    return $this;
  }

  public function page($page, $onpage)
  {
    $this->limit  = $onpage;
    $this->offset = ($page - 1) * $onpage;
    return $this;
  }

  public function table($table)
  {
    $this->table = $table;
    return $this;
  }

  public function where($mixed)
  {
    if (is_array($mixed)) {
      $this->where = $mixed;
    } elseif (!is_null($mixed)) {
      $this->where = func_get_args();
    }
    $this->make(); // Bind Values
    return $this;
  }

  public function join($table, $on, $type = NULL)
  {
    $this->join_arr[] = array('table'=> $table, 'on'=> $on, 'type'=> $type);
    return $this;
  }

  public function values($arr)
  {
    if (is_array($arr)) {
      $this->values = $arr;
      foreach ($arr as $key=> $val)
        $this->bindValue($key, $val);
    }
    return $this;
  }

  public function setNoBind($on = true)
  {
    $this->no_bind = $on;
    return $this;
  }

  public function orderby($mixed)
  {
    if (!is_null($mixed)) {
      $this->orderby = func_get_args();
    }
    return $this;
  }

  public function groupby($mixed)
  {
    if (!is_null($mixed)) {
      $this->groupby = func_get_args();
    }
    return $this;
  }

  /** PROTECTED **/
  protected function action($action)
  {
    $this->action = strtoupper($action);
    return $this;
  }

  /** STATIC **/
  public static function Prefix($prefix = NULL)
  {
    if (is_null($prefix)) {
      return self::$default_prefix;
    }
    else self::$default_prefix = $prefix;
  }

  static public function BuildWhere($mixed, $obj = NULL)
  {
    $arr = array();
    if (is_array($mixed)) {
      foreach ($mixed as $key=> $val) {
        if (is_numeric($key) && is_numeric($val)) {
          $val = array('id'=> $val);
        }
        elseif ($val === true) $val = array('is_active'=> true);

        if (is_array($val)) {
          foreach ($val as $k=> $v) {
            if (is_null($v)) {
              $arr[] = '`'.$k.'` IS NULL';
            }
            else $arr[] = self::QuoteKeyValue($k, $v, $obj, 'w_', true);
          }

        } else {
          if (is_null($val)) {
            $arr[] = '`'.$key.'` IS NULL';
          }
          else $arr[] = self::QuoteKeyValue($key, $val, $obj, 'w_', true);
        }

      }
      $mixed = join(' AND ', $arr);
    }
    return !empty ($mixed) ? ' WHERE '.$mixed : '';
  }

  static public function BuildLimit($limit, $offset = NULL)
  {
    return !empty ($limit) ? ' LIMIT '.($offset ? $offset.', ' : '').$limit : '';
  }

  static public function BuildOrderBy($orderby)
  {
    return !empty ($orderby) ? ' ORDER BY '.self::ColumnsToSQL($orderby) : '';
  }

  static public function BuildGroupBy($groupby)
  {
    return !empty ($groupby) ? ' GROUP BY '.self::ColumnsToSQL($groupby) : '';
  }

  static public function BuildJoin($table, $on, $type = NULL)
  {
    $onstr = '';
    if (is_array($on)) {
      $tmp = array();
      foreach ($on as $key=> $val) {
        if (is_numeric($key)) {
          $tmp[] = self::QuoteColumn($val);
        }
        else $tmp[] = self::QuoteColumn($key).'='.self::QuoteColumn($val);
      }
      $on = join(' AND ', $tmp);
    }
    return (!is_null($type) ? ' '.strtoupper($type) : '').' JOIN '.self::ColumnsToSQL($table).' ON '.$on;
  }

  static public function QuoteValue($val)
  {
    if (is_null($val)) return 'NULL';
    if (!ini_get('magic_quotes_gpc')) {
      $val = addslashes($val);
    }
    return is_numeric($val) ? $val : '"'.$val.'"';
  }

  static private function HasSpecChar($str)
  {
    return preg_match('/[><,\.()`\s\*=]+/is', $str);
  }

  static private function QuoteColumn($str)
  {
    return self::HasSpecChar($str) ? $str : '`'.$str.'`';
  }

  static private function QuoteKeyValue($key, $val, $obj = NULL, $prefix = NULL, $forcevalue = NULL)
  {
    if (!$forcevalue && self::HasSpecChar($val)) {
      return $val;
    }
    elseif (is_numeric($key)) return $val;
    else {
      if ($obj instanceof SQL) {
        $obj->bindValue($prefix.$key, $val);
        return '`'.$key.'`=:'.$prefix.$key;
      } else {
        return '`'.$key.'`='.self::QuoteValue($val);
      }
    }
  }

  static private function ColumnsToSQL($arr, $prefix = NULL)
  {
    $out = array();
    if (!is_array($arr)) $arr = array($arr);

    foreach ($arr as $val) {
      if (is_array($val)) {
        foreach ($val as $v)
          $out[] = self::QuoteColumn($prefix.$v);
      } else $out[] = self::QuoteColumn($prefix.$val);
    }
    return join(', ', $out);
  }

  static private function ValuesToSQL($arr, $obj = NULL, $prefix = NULL)
  {
    $out = array();
    if (!is_array($arr)) $arr = array($arr);

    foreach ($arr as $key=> $val) {
      if (is_array($val)) {
        foreach ($val as $k=> $v)
          $out[] = self::QuoteKeyValue($k, $v, $obj, $prefix, true);
      }
      else {
        $out[] = self::QuoteKeyValue($key, $val, $obj, $prefix, true);
      }
    }
    return join(', ', $out);
  }
}