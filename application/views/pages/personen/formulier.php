<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Gegevens van <?= $persoon->volledige_naam() ?></h2>
		<ol class="breadcrumb">
			<li>
				<a href="/dashboard">Home</a>
			</li>
			<li>
				<a>Personen</a>
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
							<div class="col-sm-4"><input name="persoon[voornaam]" type="text" class="form-control" value="<?= $persoon->voornaam ?>" required autofocus></div>
							<div class="col-sm-2"><input name="persoon[tussenvoegsel]" type="text" placeholder="Tussenvoegsel" class="form-control" value="<?= $persoon->tussenvoegsel ?>"></div>
							<div class="col-sm-4"><input name="persoon[achternaam]" type="text" class="form-control" value="<?= $persoon->achternaam ?>" required></div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group"><label class="col-sm-2 control-label">Adres</label>
							<div class="col-sm-4"><input name="persoon[adres]" type="text" class="form-control" value="<?= $persoon->adres ?>"></div>
							<div class="col-sm-2"><input name="persoon[postcode]" type="text" class="form-control" value="<?= $persoon->postcode ?>"></div>
							<div class="col-sm-4"><input name="persoon[woonplaats]" type="text" class="form-control" value="<?= $persoon->woonplaats ?>"></div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group"><label class="col-sm-2 control-label">E-mail</label>
							<div class="col-sm-10"><input type="email" name="persoon[email]" class="form-control" value="<?= $persoon->email ?>" required></div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group"><label class="col-sm-2 control-label">Geboortedatum</label>
							<div class="col-sm-10"><input type="text" name="persoon[geboortedatum]" placeholder="DD-MM-JJJJ" class="form-control" value="<?= Str::reverseDate($persoon->geboortedatum) ?>"></div>
						</div>
						
						<div class="hr-line-dashed"></div>
						
						<div class="form-group"><label class="col-sm-2 control-label">Lid sinds/tot</label>
							<div class="col-sm-5"><input type="text" name="persoon[lid_sinds]" placeholder="DD-MM-JJJJ" class="form-control" value="<?= Str::reverseDate($persoon->lid_sinds) ?>"></div>
							<div class="col-sm-5"><input type="text" name="persoon[lid_tot]" placeholder="DD-MM-JJJJ" class="form-control" value="<?= Str::reverseDate($persoon->lid_tot) ?>"></div>
						</div>
						
						<div class="hr-line-dashed"></div>

						<div class="form-group">
							<div class="col-sm-4 col-sm-offset-2">
								<a href="/personen/overzicht" class="btn btn-white">Terug</a>
								<button class="btn btn-primary" type="submit">Opslaan</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php if($persoon->exists): ?>
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
						<form role="form" method="POST" action="/personen/<?= $persoon->id ?>/lidmaatschapaanpassen" class="form-inline">							
							<div class="form-group"><label class="col-sm-4 control-label">Huidige Lidmaatschapstatus</label>
								<div class="col-sm-8">
									<div class="input-group">
										<select id="lidstatus" class="form-control" name="lidstatus_id">
											<?php foreach(Lidstatus::get() as $mogelijke_lidstatus): ?>
												<option value="<?= $mogelijke_lidstatus->id ?>" <?= ($lidstatus->lidstatus_id == $mogelijke_lidstatus->id) ? 'selected' : '' ?>><?= $mogelijke_lidstatus->naam ?></option>
											<?php endforeach ?>
										</select>
										<span class="input-group-btn"> <button type="submit" class="btn btn-primary">Aanpassen
									</button> </span></div>
								</div>
							</div>
						</form>
						
						<div class="hr-line-dashed"></div>
						
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
								<?php if (count($persoon->groepen) > 0): ?>
									<?php foreach ($persoon->groepen as $groep): ?>
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
	<?php endif ?>
</div>

<div class="modal fade" id="editor-modal" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="editor-title">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="firstName" class="col-sm-3 control-label">First Name</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Save changes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>

<script>
	 $(document).ready(function() {
		 $('.footable').footable();
	 });
</script>

