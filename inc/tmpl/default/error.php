<?php /** @var $this PageError */ ?>
<?= $this->header() ?>
<?= !empty($message) ? '<h3>'.$message.'</h3>' : '' ?>
<?= !empty($stack) ? '<pre>'.$stack.'</pre>' : '' ?>