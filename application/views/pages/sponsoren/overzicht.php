<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>Sponsoren</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/dashboard">Home</a>
                        </li>
                        <li>
                            Sponsoren
                        </li>
						<li class="active">
                            <strong>Overzicht</strong>
                        </li>
                    </ol>
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Alle sponsoren</h5>
                            <div class="ibox-tools">
                                <a href="" class="btn btn-primary btn-xs">Nieuwe sponsor</a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="project-list">

                                <table class="table table-hover">
                                    <tbody>
										<?php if (count($evenementen) > 0): ?>
											<?php foreach($evenementen as $evenement): ?>
												<tr style="cursor:pointer" onclick='window.location.href = ("/evenementen/<?= $evenement->id ?>/details")'>
													<td class="project-status">
														<span class="label label-<?= ($evenement->zichtbaar) ? 'primary' : 'default' ?>"><?= ($evenement->zichtbaar) ? 'Zichtbaar' : 'Niet zichtbaar' ?></span>
													</td>
													<td class="project-title">
														<a href="#"><?= $evenement->naam ?></a>
														<br/>
														<small><?= Str::reverseDate($evenement->datum) ?></small>
													</td>
													<td class="project-completion">
														<small>Beschikbare plekken: <?= ($evenement->plekken > 0) ? ($evenement->plekken - count($evenement->personen)).'/'.$evenement->plekken : 'Geen limiet' ?></small>
															<div class="progress progress-mini">
																<?php if($evenement->plekken > 0): ?>
																	<div style="width: <?= (100 / $evenement->plekken * count($evenement->personen)) ?>%;" class="progress-bar"></div>
																<?php else: ?>
																	<div style="width: 100%;" class="progress-bar"></div>
																<?php endif ?>
															</div>
													</td>
													<td class="project-people">
														<a href=""><img alt="image" class="img-circle" src="/img/profile_blank.png"></a>
														<a href=""><img alt="image" class="img-circle" src="/img/profile_blank.png"></a>
														<a href=""><img alt="image" class="img-circle" src="/img/profile_blank.png"></a>
														<a href=""><img alt="image" class="img-circle" src="/img/profile_blank.png"></a>
														<a href=""><img alt="image" class="img-circle" src="/img/profile_blank.png"></a>
													</td>
												</tr>
											<?php endforeach ?>
										<?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>