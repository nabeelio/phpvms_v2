<h3>Pilot Ranks</h3>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Rank Title</th>
	<th>Minimum Hours</th>
	<th>Rank Image</th>
	<th>Pay Rate</th>
	<th>Total Pilots</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php
foreach($ranks as $rank)
{
?>
<tr id="row<?php echo $rank->rankid;?>">
	<td align="center"><?php echo $rank->rank; ?></td>
	<td align="center"><?php echo $rank->minhours; ?></td>
	<td align="center"><img src="<?php echo $rank->rankimage; ?>" /></td>
	<td align="center"><?php echo Config::Get('MONEY_UNIT').$rank->payrate.'/hr'; ?></td>
	<td align="center"><?php echo $rank->totalpilots; ?></td>
	<td align="center" width="1%" nowrap>
	
		<button href="<?php echo SITE_URL?>/admin/action.php/pilotranking/editrank?rankid=<?php echo $rank->rankid;?>"
			id="dialog" class="jqModal {button:{icons:{primary:'ui-icon-wrench'}}}">Edit</button>
			
		<button href="<?php echo SITE_URL?>/admin/action.php/pilotranking/pilotranks" action="deleterank" 
			id="<?php echo $rank->rankid;?>" class="deleteitem {button:{icons:{primary:'ui-icon-trash'}}}">
			Delete</button>
	</td>
</tr>
<?php
}
?>
</tbody>
</table>