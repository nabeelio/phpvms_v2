<h3><?php echo $title;?></h3>
<form id="form" action="<?php echo adminaction('/operations/airlines');?>" method="post">
<dl>
<dt>Airline Code *</dt>
<dd><input name="code" type="text" value="<?php echo $airline->code; ?>" /></dd>

<dt>Airline Name *</dt>
<dd><input name="name" type="text" value="<?php echo $airline->name; ?>" /></dd>

<dt>Enabled *</dt>
<dd><?php $checked = ($airline->enabled==1)?'CHECKED':''; ?>
	<input name="enabled" type="checkbox" <?php echo $checked ?> /></dd>

<dt></dt>
<dd><input type="hidden" name="id" value="<?php echo $airline->id;?>" />
	<input type="hidden" name="action" value="<?php echo $action;?>" />
	<input type="submit" name="submit" value="<?php echo $title;?>" />
</dd>
</dl>
</form>