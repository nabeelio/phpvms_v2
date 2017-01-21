<div id="mainbox">
	<h3>Sala de Pilotos</h3>
	<div class="indent">
		<div id="profileHeader" style="width: 970px; text-align: center;">
			<p><strong>¡Bienvenido(a) nuevamente <?php echo $userinfo->firstname . ' ' . $userinfo->lastname; ?>!</strong></p>
			<img src="<?php echo PilotData::getPilotAvatar($pilotcode); ?>" />
			<br /><br />
			<img src="<?php echo $userinfo->rankimage ?>" />
		</div>
		<div id="profileDetail" style="width: 970px;">
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td valign="top" nowrap>
									<p>
										<strong>Opciones de Perfil</strong>
										<ul>
											<li><a href="<?php echo url('/profile/editprofile'); ?>">Editar mi Perfil, Email y Avatar</a></li>
											<li><a href="<?php echo url('/profile/changepassword'); ?>">Cambiar mi contraseña</a></li>
											<li><a href="<?php echo url('/profile/badge'); ?>">Ver mi Insignia</a></li>
											<li><a href="<?php echo url('/profile/stats'); ?>">Mis Estadísticas</a></li>
											<li><a href="<?php echo url('/downloads'); ?>">Ver Descargas</a></li>
										</ul>
									</p>
									<p>
										<strong>Operaciones de Vuelo</strong>
										<ul>
											<li><a href="<?php echo url('/pireps/mine');?>">Ver mis PIREPs</a></li>
											<li><a href="<?php echo url('/pireps/routesmap');?>">Ver un mapa con todos mis vuelos</a></li>
											<li><a href="<?php echo url('/pireps/filepirep');?>">Enviar un nuevo Reporte de Vuelo</a></li>
											<li><a href="<?php echo url('/schedules/view');?>">Ver Itinerarios de Vuelos</a></li>
											<li><a href="<?php echo url('/schedules/bids');?>">Ver mis Ofertas de Vuelos</a></li>		
											<li><a href="<?php echo url('/finances');?>">Ver las finanzas de mi AV</a></li>
										</ul>
									</p>
									<p>
										<strong>Mis Premios</strong><br />
										<?php
											if(!$allawards)
											{
												echo 'Todavía sin premios';
											}
											else
											{
												/* To show the image:
												<img src="<?php echo $award->image?>" alt="<?php echo $award->descrip?>" />
												*/
										?>
											<ul>
											<?php foreach($allawards as $award){ ?>
												<li><?php echo $award->name ?></li>
											<?php } ?>
											</ul>
										<?php
											}
										?>
									</p>
									<p>
										<strong>Configuración de ACARS</strong>
										<ul>
											<li>Si utiliza nuestro smartACARS, no necesita ninguna configuración</li>
											<li><a href="<?php echo actionurl('/acars/fsacarsconfig');?>">Descargue configuración para FSACARS</a></li>
											<li><a href="<?php echo actionurl('/acars/fspaxconfig');?>">Descargue configuración para FSPax</a></li>
											<li><a href="<?php echo actionurl('/acars/xacarsconfig');?>">Descargue configuración para XAcars</a></li>
											<li><strong>Para FSFK, necesita lo siguiente (Poner en Documents/FS Flight Keeper/Templates): </strong></li>
											<li><a href="<?php echo actionurl('/fsfk/vaconfig_template');?>">VA-Template.txt</a></li>
										</ul>
									</p>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<ul style="margin-top: 10px;">
							<li><strong>Su ID de Piloto: </strong> <?php echo $pilotcode; ?></li>
							<li><strong>Su Rango: </strong><?php echo $userinfo->rank;?></li>
							<?php
								if($report)
								{ ?>
									<li><strong>Último vuelo: </strong><a 
										href="<?php echo url('pireps/view/'.$report->pirepid); ?>">
										<?php echo $report->code . $report->flightnum; ?></a>
									</li>
								<?php
								}
								?>
							<li><strong>Total de Vuelos: </strong><?php echo $userinfo->totalflights?></li>
							<li><strong>Total de Horas: </strong><?php echo $userinfo->totalhours; ?></li>
							<li><strong>Total de Horas Transferidas: </strong><?php echo $userinfo->transferhours?></li>
							<li><strong>Total de Dinero: </strong><?php echo FinanceData::FormatMoney($userinfo->totalpay) ?></li>
							<?php
								if($nextrank)
								{
							?>
								<p>Le faltan <?php echo ($nextrank->minhours - $pilot_hours)?> horas
									para poder ser promovido a <?php echo $nextrank->rank?></p>
							<?php
								}
							?>
						</ul>

					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<br />