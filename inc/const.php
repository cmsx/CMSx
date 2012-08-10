<?php

// Типы полей MySQL
define ( 'DB_ID',       'INT UNSIGNED PRIMARY KEY AUTO_INCREMENT' );
define ( 'DB_INT',      'INT UNSIGNED DEFAULT 0' );
define ( 'DB_PARENT_ID','INT UNSIGNED DEFAULT NULL' );
define ( 'DB_TINYINT',  'TINYINT UNSIGNED DEFAULT 0' );
define ( 'DB_TEXT',     'LONGTEXT' );
define ( 'DB_CHAR',     'VARCHAR(250) DEFAULT NULL' );
define ( 'DB_BOOL',     'BOOL DEFAULT 0' );
define ( 'DB_PRICE',    'FLOAT(10,2)' );
define ( 'DB_TIME',     'TIMESTAMP DEFAULT 0' );
define ( 'DB_TIMECUR',  'TIMESTAMP DEFAULT CURRENT_TIMESTAMP' );
define ( 'DB_TIMELOG',  'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' );

// Типовые регулярки
define ( 'REGULAR_LOGIN', '/^[a-z0-9_-]+$/i' );
define ( 'REGULAR_EMAIL', '/^[\w._-]+@[\w._-]+\.[a-z]{2,4}$/i' );
define ( 'REGULAR_PHONE', '/^[0-9-+()\s]*$/i' );
define ( 'REGULAR_CLEAN', '/^[a-zA-Zа-яА-ЯёЁ0-9.,?!()@\s\*_-]*$/uis' );
define ( 'REGULAR_CLEANER', '[^a-zA-Zа-яА-ЯёЁ0-9.,?!()@\s_-]' );