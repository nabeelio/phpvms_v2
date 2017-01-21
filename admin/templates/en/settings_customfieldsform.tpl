<h3>Custom Fields</h3>
<?php
if(!$allfields)
{
	echo '<p>You have not added any custom fields</p><br />';
	return;
}
?>

<table id="tabledlist" class="tablesorter">
<thead>
	<tr>
		<th>Field Name</th>
		<th>Default Value</th>
		<th>Type</th>
		<th>Options</th>
	</tr>
</thead>
<tbody>
<?php
foreach($allfields as $field)
{
?>
<tr id="row<?php echo $field->fieldid;?>">
	<td align="center"><?php echo $field->title;?></td>
	<td align="center"><?php echo $field->value;?></td>
	<td align="center"><?php echo $field->type;?></td>
	<td align="center" nowrap width="1%">
		<button id="dialog" class="jqModal button" 
			href="<?php echo SITE_URL?>/admin/action.php/settings/editfield?id=<?php echo $field->fieldid;?>">
			Edit</button>
			
		<button href="<?php echo SITE_URL?>/admin/action.php/settings/customfields" 
				action="deletefield" id="<?php echo $field->fieldid;?>" class="deleteitem button">
			Delete</button>
	</td>
</tr>
<?php
}
?>
</tbody>
</table>