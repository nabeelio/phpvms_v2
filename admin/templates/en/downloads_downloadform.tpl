<h3><?php echo $title?></h3>
<form id="form" method="post" action="<?php echo adminaction('/downloads/overview');?>">
<dl>
	<dt>Select Category</dt>
	<dd>
		<?php 
			if(!$allcategories) {
				echo 'No categories have been added! You must add one first';
				$allcategories=array();
			}
		?>
		<select name="category">
		<?php
		foreach($allcategories as $category)
		{
			if($category->id == $download->pid || $_GET['cat'] == $category->id)
				$checked = ' selected ';
			else
				$checked = '';
				
			echo '<option value="'.$category->id.'" '.$checked.'>'.$category->name.'</option>';
		}
		?>	
		</select>
	</dd>
	
	<dt>Download Name</dt>
	<dd><input name="name" type="text" value="<?php echo $download->name; ?>" /></dd>
	
	<dt>Description</dt>
	<dd><input name="description" type="text" value="<?php echo $download->description; ?>" /></dd>
	
	<dt>Download Link</dt>
	<dd><input name="link" type="text" value="<?php echo $download->link; ?>" /></dd>
	
	<dt>Link to Image</dt>
	<dd><input name="image" type="text" value="<?php echo $download->image; ?>" /></dd>

	<dt></dt>
	<dd><input type="hidden" name="id" value="<?php echo $download->id;?>" />
		<input type="hidden" name="action" value="<?php echo $action;?>" />
		<input type="submit" name="submit" value="<?php echo $title;?>" />
	</dd>
</dl>
</form>