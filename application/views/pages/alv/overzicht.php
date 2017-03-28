<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Algemene Ledenvergadering</h2>
		<ol class="breadcrumb">
			<li>
				<a href="/dashboard">Home</a>
			</li>
			<li>
				<a>ALV</a>
			</li>
			<li class="active">
				<strong>Overzicht</strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-2">

	</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="wrapper wrapper-content">
				<div class="row">
					<div class="col-lg-3">
						<div class="widget style1 navy-bg">
							<div class="row">
								<div class="col-xs-4">
									<i class="fa fa-user fa-5x"></i>
								</div>
								<div class="col-xs-8 text-right">
									<span> Totaal aantal leden </span>
									<h2 class="font-bold"><?= $leden_totaal ?></h2>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="widget style1 navy-bg">
							<div class="row">
								<div class="col-xs-4">
									<i class="fa fa-user fa-5x"></i>
								</div>
								<div class="col-xs-8 text-right">
									<span> Totaal stemgerechtigde leden </span>
									<h2 class="font-bold"><?= $leden_stem ?></h2>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="widget style1 lazur-bg">
							<div class="row vertical-align">
								<div class="col-xs-3">
									<i class="fa fa-envelope fa-3x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<a class="btn btn-danger" href="/alv/genereeradresstickers" >Adresstickers genereren</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="widget style1 lazur-bg">
							<div class="row vertical-align">
								<div class="col-xs-3">
									<i class="fa fa-users fa-3x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<a class="btn btn-danger" href="/alv/genereerpresentielijst" >ALV Presentielijst genereren</a>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>