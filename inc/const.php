<?php

// Типовые регулярки
define ( 'REGULAR_LOGIN', '/^[a-z0-9_-]+$/i' );
define ( 'REGULAR_EMAIL', '/^[\w._-]+@[\w._-]+\.[a-z]{2,4}$/i' );
define ( 'REGULAR_PHONE', '/^[0-9-+()\s]*$/i' );
define ( 'REGULAR_CLEAN', '/^[a-zA-Zа-яА-ЯёЁ0-9.,?!()@\s\*_-]*$/uis' );
define ( 'REGULAR_CLEANER', '[^a-zA-Zа-яА-ЯёЁ0-9.,?!()@\s_-]' );