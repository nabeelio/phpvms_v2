<?php if(!defined('IN_PHPVMS') && IN_PHPVMS !== true) { die(); } ?>
<script type="text/javascript">
function handleChange(cb) {
if(cb.checked == true)
{if(confirm('Are you sure you want to delete all of your current airports and uploads the new ones in the CSV? It can potentially mess up your schedules or PIREPs!')){cb.checked = true;}
else{cb.checked = false;}}
}
</script>
<h3>Airport Import</h3>
<form enctype="multipart/form-data" action="<?php echo adminurl('/import/importairports');?>" method="post">
Choose your import file (*.csv): <br />
	<input name="uploadedfile" type="file" /><br />
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	
	<br />
    <input type="checkbox" name="erase_airports"  onchange='handleChange(this);'/> Delete All Airports - NOTE:This deletes all of your current airports and uploads the new ones in the CSV. It can potentially mess up your schedules or PIREPs.
	<br /><br />
	<input type="submit" value="Upload File" />

</form>