<h3><?php echo $title ?></h3>
<p>Add a custom PIREP field, which a pilot can enter information to when filing a PIREP.</p>
<form id="form" method="post" action="<?php echo SITE_URL?>/admin/action.php/settings/pirepfields">
<dl>
	<dt>Field Name</dt>
	<dd><input type="text" name="title" value="<?php echo $field->title ?>" /></dd>
	
	<dt>Field Type</dt>
	<dd><?php
		$sel = $field->type;
		$$sel = 'selected';
		?>
		<select name="type">
			<option value="text" <?php echo $text ?>>Text field</option>
			<option value="textarea" <?php echo $textarea ?>>Text area</option>
			<option value="dropdown" <?php echo $dropdown ?>>Dropdown</option>
		</select>
	
	<dt>Field Values (separate multiples with a comma)</dt>
	<dd><input type="text" name="options" value="<?php echo $field->options ?>" /></dd>
	
	<dt></dt>
	<dd><input type="hidden" name="fieldid" value="<?php echo $field->fieldid;?>" />
		<input type="hidden" name="action" value="<?php echo $action ?>" />
		<input type="submit" name="submit" value="<?php echo $title ?>" /></dd>
</dl>
</form>