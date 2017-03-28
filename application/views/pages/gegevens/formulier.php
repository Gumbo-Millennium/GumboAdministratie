<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Gegevens van <?= $persoon->volledige_naam() ?></h2>
		<ol class="breadcrumb">
			<li>
				<a href="/dashboard">Home</a>
			</li>
			<li>
				<a>Gegevens</a>
			</li>
			<li class="active">
				<strong>Bewerken</strong>
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
				<div class="ibox-title">
					<h5>Gegevens</h5>
					<div class="ibox-tools">
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
						<a class="close-link">
							<i class="fa fa-times"></i>
						</a>
					</div>
				</div>
				<div class="ibox-content">
					<form method="POST" action="" class="form-horizontal">
						<div class="form-group"><label class="col-sm-2 control-label">Naam</label>
							<div class="col-sm-4"><input name="persoon[voornaam]" type="text" class="form-control" value="<?= $persoon->voornaam ?>"></div>
							<div class="col-sm-2"><input name="persoon[tussenvoegsel]" type="text" placeholder="Tussenvoegsel" class="form-control" value="<?= $persoon->tussenvoegsel ?>"></div>
							<div class="col-sm-4"><input name="persoon[achternaam]" type="text" class="form-control" value="<?= $persoon->achternaam ?>"></div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group"><label class="col-sm-2 control-label">Adres</label>
							<div class="col-sm-4"><input name="persoon[adres]" type="text" class="form-control" value="<?= $persoon->adres ?>"></div>
							<div class="col-sm-2"><input name="persoon[postcode]" type="text" class="form-control" value="<?= $persoon->postcode ?>"></div>
							<div class="col-sm-4"><input name="persoon[woonplaats]" type="text" class="form-control" value="<?= $persoon->woonplaats ?>"></div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group"><label class="col-sm-2 control-label">E-mail</label>
							<div class="col-sm-10"><input type="email" name="persoon[email]" class="form-control" value="<?= $persoon->email ?>"></div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group"><label class="col-sm-2 control-label">Geboortedatum</label>
							<div class="col-sm-10"><input type="text" name="persoon[geboortedatum]" placeholder="DD-MM-JJJJ" class="form-control" value="<?= Str::reverseDate($persoon->geboortedatum) ?>"></div>
						</div>
						
						<div class="hr-line-dashed"></div>
						
						<div class="form-group"><label class="col-sm-2 control-label">Lid sinds/tot</label>
							<div class="col-sm-5"><input type="text" name="persoon[lid_sinds]" placeholder="DD-MM-JJJJ" class="form-control" value="<?= Str::reverseDate($persoon->lid_sinds) ?>" readonly></div>
							<div class="col-sm-5"><input type="text" name="persoon[lid_tot]" placeholder="DD-MM-JJJJ" class="form-control" value="<?= Str::reverseDate($persoon->lid_tot) ?>" readonly></div>
						</div>						
						
						<div class="hr-line-dashed"></div>
						
						<div class="form-group">
							<div class="col-lg-offset-2 col-lg-10">
								<div class="i-checks">
									<label> 
										<?= Form::checkbox('persoon[gumbode]', '1', $persoon->gumbode) ?><i></i> Ik wil de Gumbode ontvangen
									</label>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-lg-offset-2 col-lg-10">
								<div class="i-checks"><label><?= Form::checkbox('persoon[post]', '1', $persoon->post) ?><i></i> Ik wil alle post van Gumbo alleen nog maar digitaal ontvangen</label></div>
							</div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group">
							<div class="col-sm-4 col-sm-offset-2">
								<a href="/dashboard" class="btn btn-white">Terug</a>
								<button class="btn btn-primary" type="submit">Opslaan</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Lidmaatschap</h5>
					<div class="ibox-tools">
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
						<a class="close-link">
							<i class="fa fa-times"></i>
						</a>
					</div>
				</div>
				<div class="ibox-content">
					<table class="footable table table-stripped table-hover" data-sorting="true">
						<thead>
							<tr>
								<th  data-breakpoints="xs">Schooljaar</th>
								<th>Lidstatus</th>
							</tr>
						</thead>
						<tbody>								
							<?php if (count($persoon->lidstatussen()) > 0): ?>
								<?php foreach ($persoon->lidstatussen() as $lidstatus): ?>
									<tr>
										<td><?= $lidstatus->jaar ?></td>
										<td><?= $lidstatus->naam ?></td>
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
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Groepen</h5>
					<div class="ibox-tools">
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
						<a class="close-link">
							<i class="fa fa-times"></i>
						</a>
					</div>
				</div>
				<div class="ibox-content">
					<table class="footable table table-stripped table-hover" data-sorting="true">
						<thead>
							<tr>
								<th  data-breakpoints="xs">Groep</th>
								<th>Functie</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count(Auth::user()->groepen) > 0): ?>
								<?php foreach (Auth::user()->groepen as $groep): ?>
									<tr>
										<td><?= $groep->naam ?></td>
										<td><?= Rol::find($groep->rol_id)->naam ?></td>
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