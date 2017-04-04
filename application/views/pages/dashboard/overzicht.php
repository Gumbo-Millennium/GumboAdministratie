<div class="row">
	<div class="col-lg-12">
		<div class="wrapper wrapper-content">
			<div class="row">
				<?php if(Auth::user()->has_groep(Groep::BESTUUR)): ?>
				
				<div class="col-lg-6">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>Laatste gebeurtenissen</h5>
							<div class="ibox-tools">
								<a class="collapse-link">
									<i class="fa fa-chevron-up"></i>
								</a>
								<a class="close-link">
									<i class="fa fa-times"></i>
								</a>
							</div>
						</div>
						<div class="ibox-content no-padding">
							<ul class="list-group">
								<?php $logs = Log::getLogItems(); ?>
								<?php foreach($logs as $log): ?>
									<li class="list-group-item">
										<p><a class='text-info' href='#'><?= Persoon::find($log->persoon_id)->volledige_naam() ?></a> <?= $log->message ?></p>
										<small class="block text-muted"><i class="fa fa-clock-o"></i> 2 minutes ago</small>
									</li>
								<?php endforeach ?>
							</ul>
						</div>
					</div>
				</div>
				
				<div class="col-lg-6">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>Ledenopbouw</h5>
							<div class="ibox-tools">
								<span class="label label-warning-light pull-right">10 Messages</span>
							</div>
						</div>
						<div class="ibox-content">
							<div id="morris-donut-chart" ></div>
						</div>
					</div>
				</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>

<script>
	
	Morris.Donut({
        element: 'morris-donut-chart',
        data: <?= Persoon::getMorrisLedenopbouw() ?>,
        resize: true,
        colors: ['#87d6c6', '#54cdb4','#1ab394'],
    });
	
	
</script>