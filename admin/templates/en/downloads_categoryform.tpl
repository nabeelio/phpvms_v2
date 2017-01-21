<h3><?php echo $title?></h3>
<form id="form" method="post" action="<?php echo adminaction('/downloads/overview'); ?>">
<dl>
	<dt>Category Name</dt>
	<dd><input name="name" type="text" value="<?php echo $category->name; ?>" /></dd>

	<dt></dt>
	<dd><input type="hidden" name="id" value="<?php echo $category->id;?>" />
		<input type="hidden" name="action" value="<?php echo $action;?>" />
		<input type="submit" name="submit" value="<?php echo $title;?>" />
	</dd>
</dl>
</form>