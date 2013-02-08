<?php if(!defined('IN_PHPVMS') && IN_PHPVMS !== true) { die(); } ?>
<h3>Add to Group</h3>

<?php 
$total = count($freegroups);
if($total == 0) {
	echo 'No groups to add to';
	return;
}
?>
<form id="pilotgroupform" action="<?php echo adminaction('/pilotadmin/viewpilots');?>" method="POST">
<dl>
	<dt>Select Group:</dt>
	<dd><select name="groupname">
		<?php
			foreach($freegroups as $group) {
				echo '<option value="'.$group.'">'.$group.'</option>';
			}
		?>
		</select></dd>

	<dt></dt>
	<dd><input type="hidden" name="pilotid" value="<?php echo $pilotid;?>" />
		<input type="hidden" name="action" value="addgroup" />
		<input type="submit" name="submit" value="Add to Group" /></dd>
</dl>
</form>