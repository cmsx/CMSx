<?php

/** Настройка подключения к БД **/
new Connection('localhost', 'cmsx', 'qwerty', 'cmsx', 'utf8');

/** Префикс к таблицам в БД */
define ('PREFIX', 'cmsx_');

/** Адрес сайта */
define('SITE_URL', 'www.cmsx.ru');

/** Имя сайта для XML и E-mail рассылок */
define('SITE_NAME', 'New CMSx site');

/**
 * Режим разработчика -
 * вывод ошибок PHP + расширенная информация при отображении Exception в CMS
 */
define ('DEVMODE', true);