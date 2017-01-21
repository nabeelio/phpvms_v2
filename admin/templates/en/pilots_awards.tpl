<div id="awardslist">
<h3>Pilot Awards</h3>
<?php
if(!$allawards)
{
	echo 'This pilot has no awards!</div>';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Award</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php 
foreach($allawards as $award)
{
?>
<tr>
	<td><?php echo $award->name .': '.$award->descrip?></td>
	<td>
	<button href="<?php echo adminaction('/pilotadmin/pilotawards');?>" action="deleteaward" 
		id="<?php echo $award->id;?>" class="awardajaxcall {button:{icons:{primary:'ui-icon-trash'}}}" pilotid="<?php echo $award->pilotid?>">
		Delete</a>
	</td>
</tr>

<?php		
}
?>
</tbody>
</table>
</div>