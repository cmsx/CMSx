<?php

/** Инициализация системы, подключение конфига, регистрация автолоадера */

@session_start();

/** Основная папка с движком */
define ('DIR_INC', __DIR__);
/** Папка с базовыми классами */
define ('DIR_LIB', DIR_INC . '/lib');
/** Папка с классами приложения */
define ('DIR_APP', DIR_INC . '/app');
/** Папка с контроллерами */
define ('DIR_CTRL', DIR_INC . '/ctrl');
/** Папка с шаблонами */
define ('DIR_TMPL', DIR_INC . '/tmpl');
/** Папка с тестами */
define ('DIR_TEST', DIR_INC . '/test');
/** Папка с временными файлами и кешем */
define ('DIR_TEMP', realpath(DIR_INC . '/../tmp'));
/** Путь к изображениям на сайте */
define ('DIR_FILES_PATH', '/files');
/** Папка с изображениями */
define ('DIR_FILES', realpath(__DIR__ . '/..' . DIR_FILES_PATH));

/** Регистрируем папку LIB для поддержки Zend */
set_include_path(get_include_path() . PATH_SEPARATOR . DIR_LIB);

// Автозагрузчик
require_once DIR_LIB . '/Autoloader.php';
Autoloader::Register(DIR_LIB, DIR_CTRL, DIR_APP);

// Константы
require_once __DIR__ . '/const.php';

// Пользовательский конфиг
if (!defined('NO_CONFIG')) {
  require_once __DIR__ . '/config.php';
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

/** Директория для шаблонов */
Template::SetTemplatesDir(DIR_TMPL);

/** Дополнительная инициализация */
require_once __DIR__ . '/custom.init.php';
