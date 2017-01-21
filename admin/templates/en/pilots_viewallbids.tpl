<h3>Current Open Bids</h3>
<?php /*echo '<pre>'; print_r($allbids); echo '</pre>';*/ 
if(!$allbids)
{
	echo 'There are no bids!';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Route</th>
	<th>Pilot</th>	
	<th>Day Filed</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php
foreach($allbids as $bid)
{?>
<tr id="row<?php echo $bid->bidid?>">
	<td><?php echo $bid->code.$bid->flightnum."({$bid->depicao} - {$bid->arricao})"?></td>
	<td><?php echo PilotData::GetPilotCode($bid->code, $bid->flightnum).' - '.$bid->firstname.' '.$bid->lastname; ?></td>
	<td><?php echo $bid->dateadded; ?></td>
	<td>
	<button href="<?php echo SITE_URL?>/admin/action.php/pilotadmin/viewbids" action="deletebid"
		id="<?php echo $bid->bidid;?>" class="deleteitem {button:{icons:{primary:'ui-icon-trash'}}}">
			Delete</button>
		</td>
	</tr>
<?php } ?>
</tbody>
</table>