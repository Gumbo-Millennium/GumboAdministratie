<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gumbo Millennium | Wachtwoord</title>

    <?= HTML::style('css/bootstrap.min.css') ?>
	<?= HTML::style('css/animate.css') ?>
    <?= HTML::style('css/style.css') ?>
    <?= HTML::style('font-awesome/css/font-awesome.css') ?>
	<?= HTML::style('externals/toastr/toastr.css') ?>
	
	<?= HTML::script('js/jquery-2.1.3.min.js') ?>
	<?= HTML::script('externals/toastr/toastr.js') ?>

</head>

<body class="gray-bg">
	<?php if (Session::has('error')): ?>
		<script type="text/javascript">
			toastr.error('De wachtwoorden komen niet overeen of het wachtwoord bevat minder dan 8 karakters')
		</script>
	<?php endif ?>

    <div class="passwordBox animated fadeInDown">
        <div class="row">

            <div class="col-md-12">
                <div class="ibox-content">

                    <h2 class="font-bold">Wachwoord herstellen</h2>

                    <div class="row">

                        <div class="col-lg-12">
                            <form class="m-t" role="form" action="" method="POST">
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="Wachtwoord" required>
									<input type="password" name="confirm_password" class="form-control" placeholder="Herhaal wachtwoord" required>
                                </div>

                                <button type="submit" class="btn btn-primary block full-width m-b">Wachtwoord herstellen</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>

</html>
