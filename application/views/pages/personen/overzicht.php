<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Personen</h2>
		<ol class="breadcrumb">
			<li>
				<a href="index.html">Home</a>
			</li>
			<li>
				<a>Personen</a>
			</li>
			<li class="active">
				<strong>Overzicht</strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-2">
		<h2>
			<div class="text-center">
				<a class="btn btn-primary" href="/personen/nieuw">Nieuw lid toevoegen</a>
			</div>
		</h2>
	</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<table class="footable table table-stripped table-hover" data-sorting="true">
						<thead>
							<tr>
								<th data-breakpoints="xs" data-type="number">Lidnummer</th>
								<th>Lidstatus</th>
								<th>Voornaam</th>
								<th>Achternaam</th>
								<th>Adres</th>
								<th>Postcode</th>
								<th>Woonplaats</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($personen) > 0): ?>
								<?php foreach($personen as $persoon): ?>
									<tr style="cursor:pointer" onclick='window.location.href = ("/personen/<?= $persoon->id ?>/bewerken")'>
										<td><?= $persoon->id ?></td>
										<td><?= Lidstatus::find($persoon->lidstatus_id)->naam ?></td>
										<td><?= $persoon->voornaam ?></td>
										<td><?= $persoon->achternaam ?><?= ($persoon->tussenvoegsel != '') ? ', '.$persoon->tussenvoegsel : '' ?></td>
										<td><?= $persoon->adres ?></td>
										<td><?= $persoon->postcode ?></td>
										<td><?= $persoon->woonplaats ?></td>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5">
									<ul class="pagination pull-right"></ul>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	 $(document).ready(function() {
		 $('.footable').footable();
	 });
</script>