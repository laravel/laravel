<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Laravel - A Framework For Web Artisans</title>
	</head>
	<body>
		<?= Form::open('home') ?>
			<?= Form::input('text', 'email'); ?>
			<?= Form::input('password', 'password'); ?>
			<?= Form::submit('Go!'); ?>
		<?php echo Form::close() ?>
	</body>
</html>