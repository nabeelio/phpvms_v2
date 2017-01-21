<h3><?php echo $title?></h3>
<form id="form" action="<?php echo SITE_URL?>/admin/action.php/pilotranking/pilotranks" method="post">
<dl>
	<dt>Rank Title</dt>
	<dd><input name="rank" type="text" value="<?php echo $rank->rank;?>" /></dd>
	
	<dt>Order/Minimum Hours</dt>
	<dd><input name="minhours" type="text" value="<?php echo $rank->minhours;?>" />
		<p>The order of this rank (if ranks are manually set), or the minimum hours required.</p>
	</dd>
	
	<dt>Pay Rate</dt>
	<dd><input name="payrate" type="text" value="<?php echo $rank->payrate;?>" />
		<p>Enter the hourly pay rate for this rank</p>
	</dd>
	
	<dt>Image URL</dt>
	<dd><input name="rankimage" type="text" value="<?php echo $rank->rankimage;?>" />
		<p>Enter the full URL, or path from root to the image</p>
	</dd>
	
	<dt></dt>
	<dd><input type="hidden" name="rankid" value="<?php echo $rank->rankid;?>" />
		<input type="hidden" name="action" value="<?php echo $action;?>">
		<input type="submit" name="submit" value="<?php echo $title;?>" /></dd> 
</dl>
</form>