<?php if(!defined('IN_PHPVMS') && IN_PHPVMS !== true) { die(); } ?>
<h3>CSV Airport Import</h3>
<p><strong>Instructions</strong> - You can import your airports from CSV. You can download
a template CSV from <a href="<?php echo SITE_URL ?>/admin/lib/airport_template.csv">here</a>. The following
must be done:</p>
<ol>
  <li>The airport icao must be added, or import will fail</li>
	<li>You can leave out the header, but if it is there, <strong>check off the box tht the first line is a header.</strong></li>
	<li>Hub column - 1 for hub, 0 for no hub. Blank defaults to enabled</li>
	
</ol>

<form enctype="multipart/form-data" action="<?php echo adminurl('/import/importairports');?>" method="post">
Choose your import file (*.csv): <br />
	<input name="uploadedfile" type="file" /><br />
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	
	<br />
	<input type="checkbox" name="header" checked /> First line of CSV is the header
	<br />
	<input type="checkbox" name="erase_airports" /> Delete all old airports - NOTE: This completely deletes every single of your airports, although, if you need to clear your airpots list, then check this.
	<br /><br />
	<input type="submit" value="Upload File" />

</form>
