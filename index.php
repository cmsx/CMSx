<?php

define('START_TIME', microtime(true));

require_once __DIR__.'/inc/init.php';

$front = new Front();
$front->route();