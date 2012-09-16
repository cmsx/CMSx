<?php /** @var $this Page */ ?>
<html>
<head>
<title><?= $this->title() ?></title>
<?= $this->keywords() ?>
<?= $this->description() ?>
<?= $this->css() ?>
<?= $this->canonical() ?>
<?= $this->meta() ?>
</head>

<body>
<?= $this->body(); ?>

<!-- JS -->
<?= $this->js() ?>
<!--  /JS -->
</body>
</html>