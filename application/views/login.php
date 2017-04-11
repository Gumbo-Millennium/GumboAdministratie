<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Gumbo | Login</title>

		<?= HTML::style('externals/toastr/toastr.min.css') ?>
		<?= HTML::style('css/bootstrap.min.css') ?>
		<?= HTML::style('font-awesome/css/font-awesome.css') ?>

		<?= HTML::style('css/animate.css') ?>
		<?= HTML::style('css/style.css') ?>

		<?= HTML::script('js/jquery-2.1.3.min.js') ?>
		<?= HTML::script('js/bootstrap.min.js') ?>
		<?= HTML::script('externals/toastr/toastr.min.js') ?>
	</head>

	<body class="gray-bg">
		<?php if (Session::has('error')): ?>
			<script type="text/javascript">
				toastr.warning('Onjuiste combinatie van gegevens ingevoerd')
			</script>
		<?php endif ?>
		<?php if (Session::has('message')): ?>
			<script type="text/javascript">
				toastr.success('<?= Session::get('message') ?>')
			</script>
		<?php endif ?>
		
		<div class="middle-box text-center loginscreen animated fadeInDown">
			<div>
				<div>
					<img src="img/gumbo.png" width="250" />
				</div>
				<form class="m-t" role="form" method="POST" action="">
					<div class="form-group">
						<input type="email" name="email" class="form-control" placeholder="E-mail" required="">
					</div>
					<div class="form-group">
						<input type="password" name="password" class="form-control" placeholder="Wachtwoord" required="">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">Login</button>
				</form>

				<a href="/lostpass"><small>Wachtwoord vergeten?</small></a>
			</div>
		</div>
	</body>
</html>
