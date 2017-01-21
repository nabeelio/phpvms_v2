<h3>Awards</h3>
<?php
if(!$awards){ echo 'No awards have been added yet!'; return;}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Award</th>
	<th>Description</th>
	<th>Image</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php
foreach($awards as $aw)
{
?>
<tr id="row<?php echo $aw->awardid;?>">
	<td align="center"><?php echo $aw->name; ?></td>
	<td align="center"><?php echo $aw->descrip; ?></td>
	<td align="center"><img src="<?php echo $aw->image; ?>" /></td>
	<td align="center" width="1%" nowrap>
		<button id="dialog" class="jqModal {button:{icons:{primary:'ui-icon-wrench'}}}" 
			href="<?php echo SITE_URL?>/admin/action.php/pilotranking/editaward?awardid=<?php echo $aw->awardid;?>">
			Edit</button>
			
		<button href="<?php echo SITE_URL?>/admin/action.php/pilotranking/awards" action="deleteaward" 
			id="<?php echo $aw->awardid;?>" class="deleteitem {button:{icons:{primary:'ui-icon-trash'}}}">
			Delete</button>
	</td>
</tr>
<?php
}
?>
</tbody>
</table>