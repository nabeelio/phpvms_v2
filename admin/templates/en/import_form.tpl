<h3>CSV Import</h3>
<p><strong>Instructions</strong> - You can import your flight plans from CSV. You can download
a template CSV from <a href="<?php echo SITE_URL ?>/admin/lib/template.csv">here</a>. The following
must be done:</p>
<ol>
	<li>The airline code must be added, or import will fail</li>
	<li>You can leave out the header, but if it is there, <strong>check off the box</strong></li>
	<li>All of the columns can be there, but only the route, leg, distance, and flighttime can be blank</li>
	<li>Routes which already exist (based on the code and flight number) will not be re-added</li>
	<li>Aircraft must be added by registration</li>
	<li>Flight Type can be "P" (Passenger), "C" (Cargo) or "H" (Charter). Enter it without quotes</li>
	<li>Days of week - last column - enter 0-6 for Sunday (0) to Saturday (6): ie: Monday, Wed, Fri, Sat flights will be: 1356 as the value</li>
	<li>Enabled column - 1 for enabled, 0 for disabled. Blank defaults to enabled</li>
	<li><strong>Remove the leg column</strong> - legs are no longer counted, so please remove that column</li>
	
</ol>

<form enctype="multipart/form-data" action="<?php echo adminurl('/import/processimport');?>" method="post">
Choose your import file (*.csv): <br />
	<input name="uploadedfile" type="file" /><br />
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	
	<br />
	<input type="checkbox" name="header" checked /> First line of CSV is the header
	<br />
	<input type="checkbox" name="erase_routes" /> Delete all previous routes - NOTE: this could potentially mess up any PIREPS from ACARS flights which are currently in progress, whose routes have changed.
	<br /><br />
	<input type="submit" value="Upload File" />

</form>