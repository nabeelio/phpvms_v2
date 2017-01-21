<table id="tabledlist" class="tablesorter">
<tbody>
<?php
	foreach($pilotdata as $pilots) {
?>
<td colspan="6"><h3><?php echo $pilots['title']; ?></h3></td>
<tr>
	<th>Pilot ID</th>
	<th>Name</th>
	<th>Rank</th>
	<th>Flights</th>
	<th>Hours</th>
	<th>VATSIM PID</th>
</tr>
<?php
	if(is_array($pilots['pilots'])) {
		foreach($pilots['pilots'] as $pilot)
		{
			/* 
				To include a custom field, use the following example:

				<td>
					<?php echo PilotData::GetFieldValue($pilot->pilotid, 'VATSIM ID'); ?>
				</td>

				For instance, if you added a field called "IVAO Callsign":

				echo PilotData::GetFieldValue($pilot->pilotid, 'IVAO Callsign');		
			 */
	 
			 // To skip a retired pilot, uncomment the next line:
			 //if($pilot->retired == 1) { continue; }
?>
<tr>
	<td width="1%" nowrap><a href="<?php echo url('/profile/view/'.$pilot->pilotid);?>">
			<?php echo PilotData::GetPilotCode($pilot->code, $pilot->pilotid)?></a>
	</td>
	<td>
		<img src="<?php echo Countries::getCountryImage($pilot->location);?>" 
			alt="<?php echo Countries::getCountryName($pilot->location);?>" />
			
		<?php echo $pilot->firstname.' '.$pilot->lastname?>
	</td>
	<td><img src="<?php echo $pilot->rankimage?>" alt="<?php echo $pilot->rank;?>" /></td>
	<td><?php echo $pilot->totalflights?></td>
	<td><?php echo Util::AddTime($pilot->totalhours, $pilot->transferhours); ?></td>
	<td><?php echo PilotData::GetFieldValue($pilot->pilotid, 'VATSIM PID'); ?></td>
</tr>
<?php
		}
	}
	else {
?>
	<tr>
		<td colspan="6"><p align="center">There are no pilots!</p></td>
	</tr>
<?php
	}
?>
<?php
	}
?>
</tbody>
</table>
