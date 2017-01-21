<h3>User Groups</h3>
<?php
if(!$allgroups)
{
	echo 'There are no groups';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Group Name</th>
	<th>Group ID</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php
foreach($allgroups as $group)
{
?>
<tr>
	<td align="center"><?php echo $group->name; ?></td>
	<td align="center"><?php echo $group->groupid; ?></td>
	<td align="center" width="1%" nowrap>
	<?php
	if($group->name!='Administrators')
	{
	?>
		<a class="button {button:{icons:{primary:'ui-icon-wrench'}}}" 
			href="<?php echo adminurl('/pilotadmin/editgroup/?groupid='.$group->groupid);?>">
			Edit</a>	
	<?php
	}
	else
		echo 'This group cannot be renamed or deleted';
	?>
	</td>
</tr>
<?php
}
?>
</tbody>
</table>