<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title>Gumbo Millennium</title>

		<?= HTML::style('externals/toastr/toastr.min.css') ?>
		<?= HTML::style('css/bootstrap.min.css') ?>
		<?= HTML::style('font-awesome/css/font-awesome.css') ?>
		
		<?= HTML::style('externals/footable/css/footable.standalone.css') ?>
		<?= HTML::style('externals/fullcalendar/fullcalendar.css') ?>

		<?= HTML::style('css/animate.css') ?>
		<?= HTML::style('css/style.css') ?>
		
		<!-- Mainly scripts -->
		<?= HTML::script('js/jquery-2.1.3.min.js') ?>
		<?= HTML::script('js/bootstrap.min.js') ?>
		<?= HTML::script('js/raphael.min.js') ?>
		<?= HTML::script('js/plugins/metisMenu/metisMenu.js') ?>
		<?= HTML::script('js/plugins/slimscroll/jquery.slimscroll.min.js') ?>
		<?= HTML::script('js/plugins/morris/morris.js') ?>

		<!-- Custom and plugin javascript -->
		<?= HTML::script('js/inspinia.js') ?>
		<?= HTML::script('js/plugins/pace/pace.min.js') ?>

		<!-- Toastr -->
		<?= HTML::script('externals/toastr/toastr.min.js') ?>
		<?= HTML::script('externals/footable/js/footable.min.js') ?>
		<?= HTML::script('externals/fullcalendar/lib/moment.min.js') ?>
		<?= HTML::script('externals/fullcalendar/fullcalendar.min.js') ?>
		
		<script>
			toastr.options = {
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 3000
                };
		</script>
		
	</head>

	<body>
		<?php if (Session::has('errors')): ?>
			<?php foreach (Session::get('errors') as $error): ?>
				<script type="text/javascript">
					toastr.warning('<?= $error; ?>')
				</script>
			<?php endforeach ?>
		<?php endif ?>

		<?php if (Session::has('success')): ?>
			<?php if(is_array(Session::get('success'))): ?>
				<?php foreach (Session::get('success') as $success): ?>
					<script type="text/javascript">
						toastr.success('<?= $success; ?>')
					</script>
				<?php endforeach ?>
			<?php else: ?>
				<script type="text/javascript">
					toastr.success('De wijzigingen zijn opgeslagen')
				</script>
			<?php endif ?>
		<?php endif ?>
				
		<div id="wrapper">
			<nav class="navbar-default navbar-static-side" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav metismenu" id="side-menu">
						<li class="nav-header">
							<div class="dropdown profile-element"> <span>
									<img alt="image" class="img-circle" width="45" src="/img/profile_blank.png" />
								</span>
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">
									<span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?= Auth::user()->volledige_naam() ?></strong>
										</span> <span class="text-muted text-xs block"><?= Auth::user()->lidstatus() ?> <b class="caret"></b></span> </span> </a>
								<ul class="dropdown-menu animated fadeInRight m-t-xs">
									<li><a href="/gegevens">Mijn gegevens</a></li>
									<li class="divider"></li>
									<li><a href="/logout">Uitloggen</a></li>
								</ul>
							</div>
							<div class="logo-element">
								Gumbo
							</div>
						</li>
						<li <?= in_array('dashboard', explode('/', Request::uri())) ? 'class="active"' : '' ?>>
							<a href="/dashboard"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>
						</li>
						<li <?= in_array('gegevens', explode('/', Request::uri())) ? 'class="active"' : '' ?>>
							<a href="/gegevens"><i class="fa fa-user"></i> <span class="nav-label">Mijn gegevens</span></a>
						</li>
						
						<?php if (Auth::user()->has_groep(Groep::BESTUUR)): ?>
							<li <?= (count(array_intersect(explode('/', Request::uri()), array('agenda', 'alv', 'personen', 'mailchimp', 'sponsoren'))) > 0) ? 'class="active"' : '' ?>>
								<a href="#"><i class="fa fa-university"></i> <span class="nav-label">Bestuur</span><span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li <?= in_array('agenda', explode('/', Request::uri())) ? 'class="active"' : '' ?>>
										<a href="/agenda/overzicht"><i class="fa fa-calendar"></i> <span class="nav-label">Agenda</span></a>
									</li>
								</ul>
								<ul class="nav nav-second-level collapse">
									<li <?= in_array('alv', explode('/', Request::uri())) ? 'class="active"' : '' ?>>
										<a href="/alv/overzicht"><i class="fa fa-comments"></i> <span class="nav-label">ALV</span></a>
									</li>
								</ul>
								<ul class="nav nav-second-level collapse">
									<li <?= in_array('personen', explode('/', Request::uri())) ? 'class="active"' : '' ?>>
										<a href="/personen/overzicht"><i class="fa fa-users"></i> <span class="nav-label">Leden</span></a>
									</li>
								</ul>
								<ul class="nav nav-second-level collapse">
									<li <?= in_array('sponsoren', explode('/', Request::uri())) ? 'class="active"' : '' ?>>
										<a href="/sponsoren/overzicht"><i class="fa fa-users"></i> <span class="nav-label">Sponsoren</span></a>
									</li>
								</ul>
								<ul class="nav nav-second-level collapse">
									<li <?= in_array('mailchimp', explode('/', Request::uri())) ? 'class="active"' : '' ?>>
										<a href="/mailchimp/synchronisatie"><i class="fa fa-envelope"></i> <span class="nav-label">Mailchimp</span></a>
									</li>
								</ul>
							</li>
						<?php endif ?>
						
						<?php foreach($types as $type_db => $type_naam): ?>
							<?php if (Auth::user()->has_groeptype($type_db)): ?>
							<li <?= in_array($type_db, explode('/', Request::uri())) ? 'class="active"' : '' ?>>
									<a href="#"><i class="fa fa-sitemap"></i> <span class="nav-label"><?= $type_naam ?></span><span class="fa arrow"></span></a>
									<ul class="nav nav-second-level collapse">
										<?php if(count($groepen) > 0): ?>
											<?php foreach($groepen as $groep): ?>
												<?php if($groep->type == $type_db): ?>
													<li <?= in_array($groep->id, explode('/', Request::uri())) ? 'class="active"' : '' ?>>
														<a href="/groepen/<?= $type_db ?>/<?= $groep->id ?>/bewerken"><?= $groep->naam ?></a>
													</li>
												<?php endif ?>
											<?php endforeach ?>
										<?php endif ?>
									</ul>
								</li>
							<?php endif ?>
						<?php endforeach ?>
					</ul>

				</div>
			</nav>

			<div id="page-wrapper" class="gray-bg dashbard-1">
				<div class="row border-bottom">
					<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
						<div class="navbar-header">
							<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
							<form role="search" class="navbar-form-custom" method="GET" action="/zoeken">
								<div class="form-group">
									<input type="text" placeholder="Zoek naar iets..." class="form-control" name="q" id="top-search">
								</div>
							</form>
						</div>
						<ul class="nav navbar-top-links navbar-right">
							<li>
								<a href="/logout">
									<i class="fa fa-sign-out"></i> Uitloggen
								</a>
							</li>
						</ul>

					</nav>
				</div>

				<?= $pagina ?>

			</div>
	</body>
</html>
