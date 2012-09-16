<?php

/** Инициализация системы, подключение конфига, регистрация автолоадера */

@session_start();

/** Основная папка с движком */
define ('DIR_INC', __DIR__);
/** Папка с базовыми классами */
define ('DIR_LIB', __DIR__.'/lib');
/** Папка с классами приложения */
define ('DIR_APP', __DIR__.'/app');
/** Папка с контроллерами */
define ('DIR_CTRL', __DIR__.'/ctrl');
/** Папка с шаблонами */
define ('DIR_TMPL', __DIR__.'/tmpl');
/** Папка с тестами */
define ('DIR_TEST', __DIR__.'/test');
/** Папка с временными файлами и кешем */
define ('DIR_TEMP', realpath(__DIR__.'/../tmp'));
/** Путь к изображениям на сайте */
define ('DIR_FILES_PATH', '/files');
/** Папка с изображениями */
define ('DIR_FILES', realpath(__DIR__.'/..'.DIR_FILES_PATH));

/** Регистрируем папку LIB для поддержки Zend */
set_include_path(get_include_path().PATH_SEPARATOR.DIR_LIB);

// Автозагрузчик
require_once DIR_LIB.'/Autoloader.php';
Autoloader::Register(DIR_LIB, DIR_CTRL, DIR_APP);

// Константы
require_once __DIR__.'/const.php';

// Пользовательский конфиг
if (!defined('NO_CONFIG')) {
  require_once __DIR__.'/config.php';
  SQL::SetPrefix(PREFIX);
  SQL::SetConnection(Connection::Get());
} else {
  define('DEVMODE', true);
}

/** Вывод ошибок */
if (DEVMODE) {
  ini_set('error_level', E_ALL);
  ini_set('display_errors', 1);
} else {
  ini_set('display_errors', 0);
}

Template::SetTemplatesDir(DIR_TMPL);