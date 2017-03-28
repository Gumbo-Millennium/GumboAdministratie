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
								<?= Log::getLogItems(); ?>
								<li class="list-group-item">
									<p><a class="text-info" href="#">@Kevin Smith</a> Lorem ipsum unknown printer took a galley </p>
									<small class="block text-muted"><i class="fa fa-clock-o"></i> 2 minuts ago</small>
								</li>
								<li class="list-group-item ">
									<p><a class="text-info" href="#">@Jonathan Febrick</a> The standard chunk of Lorem Ipsum</p>
									<small class="block text-muted"><i class="fa fa-clock-o"></i> 1 hour ago</small>
								</li>
								<li class="list-group-item">
									<p><a class="text-info" href="#">@Alan Marry</a> I belive that. Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
									<small class="block text-muted"><i class="fa fa-clock-o"></i> 1 minuts ago</small>
								</li>
								<li class="list-group-item">
									<p><a class="text-info" href="#">@Kevin Smith</a> Lorem ipsum unknown printer took a galley </p>
									<small class="block text-muted"><i class="fa fa-clock-o"></i> 2 minuts ago</small>
								</li>
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