<h3>Lista de PIREPs</h3>
<p><?php if(isset($descrip)) { echo $descrip; }?></p>
<?php
if(!$pireps)
{
	echo '<p>No se han encontrado reportes</p>';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Número de Vuelo</th>
	<th>Salida</th>
	<th>Llegada</th>
	<th>Aeronave</th>
	<th>Tiempo de Vuelo</th>
	<th>Fecha de Envío</th>
	<th>Estado</th>
	<?php
	// Only show this column if they're logged in, and the pilot viewing is the
	//	owner/submitter of the PIREPs
	if(Auth::LoggedIn() && Auth::$userinfo->pilotid == $userinfo->pilotid)
	{
		echo '<th>Opciones</th>';
	}
	?>
</tr>
</thead>
<tbody>
<?php
foreach($pireps as $report)
{
?>
<tr>
	<td align="center">
		<a href="<?php echo url('/pireps/view/'.$report->pirepid);?>"><?php echo $report->code . $report->flightnum; ?></a>
	</td>
	<td align="center"><?php echo $report->depicao; ?></td>
	<td align="center"><?php echo $report->arricao; ?></td>
	<td align="center"><?php echo $report->aircraft . " ($report->registration)"; ?></td>
	<td align="center"><?php echo $report->flighttime; ?></td>
	<td align="center"><?php echo date(DATE_FORMAT, $report->submitdate); ?></td>
	<td align="center">
		<?php
		
		if($report->accepted == PIREP_ACCEPTED)
			echo '<div id="success">Aceptado</div>';
		elseif($report->accepted == PIREP_REJECTED)
			echo '<div id="error">Rechazado</div>';
		elseif($report->accepted == PIREP_PENDING)
			echo '<div id="error">Aprobación Pendiente</div>';
		elseif($report->accepted == PIREP_INPROGRESS)
			echo '<div id="error">Vuelo en Progreso</div>';
		
		?>
	</td>
	<?php
	// Only show this column if they're logged in, and the pilot viewing is the
	//	owner/submitter of the PIREPs
	if(Auth::LoggedIn() && Auth::$userinfo->pilotid == $report->pilotid)
	{
		?>
	<td align="right">
		<a href="<?php echo url('/pireps/addcomment?id='.$report->pirepid);?>">Agregar Comentario</a><br />
		<a href="<?php echo url('/pireps/editpirep?id='.$report->pirepid);?>">Editar PIREP</a>
	</td>
	<?php
	}
	?>
</tr>
<?php
}
?>
</tbody>
</table>