<h3><?php echo $title?></h3>
<form id="form" action="<?php echo SITE_URL?>/admin/action.php/pilotranking/awards" method="post">
<dl>
	<dt>Award Name</dt>
	<dd><input name="name" type="text" value="<?php echo $award->name;?>" /></dd>
	
	<dt>Description</dt>
	<dd><input name="descrip" type="text" value="<?php echo $award->descrip;?>" /></dd>
		
	<dt>Image URL</dt>
	<dd><input name="image" type="text" value="<?php echo $award->image;?>" />
		<p>Enter the full URL, or path from root to the image</p>
	</dd>
	
	<dt></dt>
	<dd><input type="hidden" name="awardid" value="<?php echo $award->awardid;?>" />
		<input type="hidden" name="action" value="<?php echo $action;?>">
		<input type="submit" name="submit" value="<?php echo $title;?>" /></dd> 
</dl>
</form>