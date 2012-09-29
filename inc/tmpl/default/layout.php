<?php /** @var $this Page */ ?>
<?= $this->doctype() ?>
<?= $this->html() ?>
<head>
<?= $this->charset() ?>
<?= $this->title() ?>
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
<!-- /JS -->
</body>
</html>