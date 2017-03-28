<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2><?= Str::title($groep->naam) ?></h2>
		<ol class="breadcrumb">
			<li>
				<a href="/dashboard">Home</a>
			</li>
			<li>
				<a>Groepen</a>
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
						<div class="form-group">
							<label class="col-sm-1 control-label">Naam</label>
							<div class="col-sm-11">
								<input name="groep[naam]" type="text" class="form-control" value="<?= $groep->naam ?>">
							</div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="form-group">
							<label class="col-sm-1 control-label">Omschrijving</label>
							<div class="col-sm-11">
								<textarea name="persoon[adres]" class="form-control"><?= $groep->omschrijving ?></textarea>
							</div>
						</div>

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
					<h5>Personen</h5>
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
					<form role="form" method="POST" action="" class="form-inline">							
						<div class="form-group"><label class="col-sm-2 control-label">Lid toevoegen</label>
							<div class="col-sm-10">
								<div class="input-group">
									<select id="lidstatus" name="lidstatus_id">
										<?php foreach(Persoon::order_by('voornaam')->get() as $persoon): ?>
											<option value="<?= $persoon->id ?>"><?= $persoon->volledige_naam() ?></option>
										<?php endforeach ?>
									</select>
									<select id="lidstatus" name="lidstatus_id">
										<?php foreach(Rol::order_by('naam')->get() as $rol): ?>
											<option value="<?= $rol->id ?>"><?= $rol->naam ?></option>
										<?php endforeach ?>
									</select>
									<span class="input-group-btn"> <button type="submit" class="btn btn-primary">Aanpassen</button></span>
								</div>
							</div>
						</div>
					</form>
						
					<div class="hr-line-dashed"></div>
						
					<table class="footable table table-stripped table-hover" data-sorting="true">
						<thead>
							<tr>
								<th  data-breakpoints="xs">Groep</th>
								<th>Functie</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($groep->personen) > 0): ?>
								<?php foreach ($groep->personen as $persoon): ?>
									<tr>
										<td><?= $persoon->volledige_naam() ?></td>
										<td><?= Rol::find($persoon->rol_id)->naam ?></td>
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