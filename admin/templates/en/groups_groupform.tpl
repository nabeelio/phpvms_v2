<h3><?php echo $title; ?></h3>
<form action="<?php echo adminurl('/pilotadmin/pilotgroups');?>" method="post">

<table id="tabledlist" class="tablesorter">
<tbody>
	<tr>
		<td>Group Name: </td>
		<td><input type="text" name="name" value="<?php echo $group->name?>" /></td>
	</tr>

	<tr>
		<td>Group Permissions: </td>
		<td>
			<?php if($group->permissions == 0) { $checked = 'checked'; } ?>
			<input type="checkbox" name="permissions[]" value="0" <?php echo $checked;?> />No admin access<br />
		<?php
		
		foreach($permission_set as $p_name=>$p_value)
		{
			# Does group have this permission?
			
			if(PilotGroups::check_permission($group->permissions, $p_value))
				$checked = 'checked';
			else
				$checked = '';
				
			echo '<input type="checkbox" name="permissions[]" value="'.$p_value.'" '.$checked.' />'.$p_name.'<br />';
			
			
		}
		
		?>		
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="hidden" name="action" value="<?php echo $action;?>" />
			<input type="hidden" name="groupid" value="<?php echo $group->groupid;?>" />
			<input type="submit" name="submit" value="Save" />		
		</td>
	</tr>

</tbody>
</table>
</form>