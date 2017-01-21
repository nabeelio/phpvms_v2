<?php
if(!$pilotgroups)
{
	echo '<br />This user is not in any groups!<br /><br />';
}
else
{
?>
<table id="tabledlist" class="tablesorter" style="float: left">
<thead>
	<tr>
		<th>Group Name</th>	
		<th>Options</th>
	</tr>
</thead>
<tbody>

<?php 
	foreach($pilotgroups as $group)
	{
?>
	<tr>
		<td><?php echo $group->name;?></td>
		<td>
		<?php
		if(PilotGroups::group_has_perm(Auth::$usergroups, FULL_ADMIN)) 
		{ ?>
			<button href="<?php echo adminaction('/pilotadmin/viewpilots');?>" pilotid="<?php echo $pilotid;?>" 
				action="removegroup" id="<?php echo $group->groupid;?>" 
				class="pilotgroupajax button {button:{icons:{primary:'ui-icon-trash'}}}">Remove</button></td>
		<?php
		} ?>
	</tr>		
	
<?php
	}
}
?>
</tbody>
</table>
<div style="clear: both;"></div>
<?php
if(PilotGroups::group_has_perm(Auth::$usergroups, FULL_ADMIN)) 
{
?>
<h3>Add to Group</h3>

<?php 
$total = count($freegroups);

if($total == 0)
{
	echo 'No groups to add to';
	return;
}
?>
<form id="selectpilotgroup" action="<?php echo adminaction('/pilotadmin/viewpilots');?>" method="post">
<dl>
	<dt>Select Group:</dt>
	<dd><select name="groupname">
		<?php
		for($i=0;$i<$total;$i++)
		{
			echo '<option value="'.$freegroups[$i].'">'.$freegroups[$i].'</option>';
		}
		?>
		</select></dd>

	<dt></dt>
	<dd><input type="hidden" name="pilotid" value="<?php echo $pilotid;?>" />
		<input type="hidden" name="action" value="addgroup" />
		<input type="submit" name="submit" value="Add to Group" /></dd>
</dl>
</form>
<?php
}
?>