<?php

/**
 * Автолодер
 *
 * Регистрация автозагрузчика происходит через статический метод ::Register
 * При этом указываются папки, в которых будет искаться соответствующий класс
 * Именование классов и вложенность файлов, варианты:
 * myClass                -> my/Class.php
 * MyClass                -> My/Class.php
 * MyLongClass            -> My/Long/Class.php
 * APIService             -> API/Service.php
 * API_IWantToBeLongFile  -> API/IWantToBeLongFile.php
 */

class Autoloader
{
  protected $dir_lib;
  protected $dir_app;
  protected $dir_ctrl;

  /**
   * Регистрация автолодера
   * @static
   * @param $dir_lib Базовая библиотека
   * @param $dir_ctrl Директория с контроллерами
   * @param null $dir_app Директория с классами приложения
   */
  public static function Register($dir_lib, $dir_ctrl, $dir_app = null)
  {
    $obj = new static;
    $obj
      ->setDirLib($dir_lib)
      ->setDirCtrl($dir_ctrl)
      ->setDirApp($dir_app);
    spl_autoload_register(array($obj, 'call'));
  }

  /**
   * Подключение файла, если он найден для класса
   */
  protected function call($class_name)
  {
    $file = $this->findFile($class_name);
    require_once $file;
    return true;
  }

  /**
   * Поиск файла по имени класса
   */
  protected function findFile($class_name)
  {
    $dir = $this->dir_lib;
    //Контроллеры дергаем из отдельной папки
    if (strpos($class_name, 'Controller')) {
      $file = $class_name.'.php';
      $dir  = $this->dir_ctrl;
    } else {
      $parts = self::FindParts($class_name);
      $file  = join('/', $parts).'.php';
      if (!is_null($this->dir_app)) {
        $app = $this->dir_app.'/'.$file;
        if (is_file($app)) {
          return $app;
        }
      }
    }
    //Если класс до сих пор не найден ищем в lib
    $file = $dir.'/'.$file;
    if (!is_file($file)) {
      throw new Exception('Класс '.$class_name.' не найден!', 501);
    }
    return $file;
  }

  /**
   * Определение пути по названию файла.
   * Аббревиатуры написанные заглавными буквами не разбиваются
   */
  public static function FindParts($class_name)
  {
    if (false !== strpos($class_name, '\\')) {
      return explode('\\', trim($class_name, '\\'));
    } elseif (false !== strpos($class_name, '_')) {
      return explode('_', $class_name);
    } else {
      $pass1 = preg_replace("/([a-z])([A-Z])/", "\\1 \\2", $class_name);
      $pass2 = preg_replace("/([A-Z])([A-Z][a-z])/", "\\1 \\2", $pass1);
      return explode(' ', $pass2);
    }
  }

  /**
   * Папка с классами
   */
  public function setDirLib($dir_lib)
  {
    $this->dir_lib = $dir_lib;
    return $this;
  }

  /**
   * Папка с контроллерами
   */
  public function setDirCtrl($dir_ctrl)
  {
    $this->dir_ctrl = $dir_ctrl;
    return $this;
  }

  /**
   * Папка с классами текущего приложения
   * Файлы из папки App могут перекрывать файлы из Lib
   */
  public function setDirApp($dir_app)
  {
    $this->dir_app = $dir_app;
    return $this;
  }
}