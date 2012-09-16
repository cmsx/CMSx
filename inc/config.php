<?php

/** Настройка подключения к БД **/
new Connection('localhost', 'cmsx', 'qwerty', 'cmsx', 'utf8');

/** Префикс к таблицам в БД */
define ('PREFIX', 'cmsx_');

/**
 * Режим разработчика -
 * вывод ошибок PHP + расширенная информация при отображении Exception в CMS
 */
define ('DEVMODE', true);