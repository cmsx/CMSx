<?php

/** Настройка подключения к БД **/
new Connection('localhost', 'cmsx', 'qwerty', 'cmsx', 'utf8');

/** Префикс к таблицам в БД */
define ('PREFIX', 'cmsx_');

/** Домен сайта без http://, если нужно, с www */
define ('SITE_URL', 'www.cmsx.ru');

/** Название сайта для человеков и писем */
define ('SITE_NAME', 'Новый сайт');

/**
 * Режим разработчика -
 * вывод ошибок PHP + расширенная информация при отображении Exception в CMS
 */
define ('DEVMODE', true);