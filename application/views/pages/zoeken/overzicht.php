<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Zoeken</h2>
		<ol class="breadcrumb">
			<li>
				<a href="/dashboard">Home</a>
			</li>
			<li>
				<a>Zoeken</a>
			</li>
			<li class="active">
				<strong>Resultaten</strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-2">

	</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<h2>
						<?= $count ?> resultaten gevonden voor: <span class="text-navy">“<?= $q ?>”</span>
					</h2>

					<div class="hr-line-dashed"></div>

					<?php if(count($personen) > 0): ?>
						<?php foreach($personen as $persoon): ?>
							<div class="search-result">
								<h3><a href="/personen/<?= $persoon->id ?>/bewerken"><?= $persoon->volledige_naam() ?></a></h3>
								<a href="#" class="search-link"><?= $persoon->lidstatus() ?></a>
								<p>
									<?= $persoon->adres ?><br />
									<?= $persoon->postcode.' '.$persoon->woonplaats ?>
								</p>
								<p>
									<a href="mailto:<?= $persoon->email ?>" class="search-link"><?= $persoon->email ?></a>
								</p>
							</div>
							<div class="hr-line-dashed"></div>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</div>