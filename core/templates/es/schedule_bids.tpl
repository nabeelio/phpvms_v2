<h3>Mis ofertas de vuelos</h3>
<?php
if(!$bids)
{
	echo '<p align="center">No tiene ninguna ofertas de vuelos</p>';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Número de vuelo</th>
	<th>Ruta</th>
	<th>Equipo</th>
	<th>Hora de Salida</th>
	<th>Hora de Llegada</th>
	<th>Distancia</th>
	<th>Opciones</th>
</tr>
</thead>
<tbody>
<?php
foreach($bids as $bid)
{
?>
<tr id="bid<?php echo $bid->bidid ?>">
	<td><?php echo $bid->code . $bid->flightnum; ?></td>
	<td align="center"><?php echo $bid->depicao; ?> to <?php echo $bid->arricao; ?></td>
	<td align="center"><?php echo $bid->aircraft; ?> (<?php echo $bid->registration?>)</td>
	<td><?php echo $bid->deptime;?></td>
	<td><?php echo $bid->arrtime;?></td>
	<td><?php echo $bid->distance;?></td>
	<td><a href="<?php echo url('/pireps/filepirep/'.$bid->bidid);?>">Enviar PIREP</a><br />
		<a id="<?php echo $bid->bidid; ?>" class="deleteitem" href="<?php echo url('/schedules/removebid');?>">Eliminar Oferta *</a><br />
		<a href="<?php echo url('/schedules/brief/'.$bid->id);?>">Briefing de Piloto</a><br />
		<a href="<?php echo url('/schedules/boardingpass/'.$bid->id);?>" />Pase de Abordaje</a>
		
	</td>
</tr>
<?php
}
?>
</tbody>
</table>
<p align="right">* - doble click</p>
<hr>