<?php /** @var $this PageError */ ?>
<h1><?= $this->header() ?></h1>
<?= !empty($message) ? '<h3>'.$message.'</h3>' : '' ?>
<?= !empty($stack) ? '<pre>'.$stack.'</pre>' : '' ?>