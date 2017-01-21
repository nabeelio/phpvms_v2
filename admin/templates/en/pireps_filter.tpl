<form action="<?php echo SITE_URL.'/admin/index.php/pirepadmin/viewall';?>" method="get">
<strong>Filter schedules: </strong>
<input type="text" name="query" value="<?php if($_GET['query']) { echo $_GET['query'];} else { echo '(Use % for wildcard)';}?>" onClick="this.value='';" />
<select name="type">
	<option value="code">code</option>
	<option value="flightnum">flight number</option>
	<option value="pilotid">pilotid</option>
	<option value="depapt">departure airport</option>
	<option value="arrapt">arrival airport</option>
	<option value="aircraft">aircraft type</option>
</select>

&nbsp;&nbsp;Status:
<select name="accepted">
	<option value="all">All</option>
	<option value="<?php echo PIREP_PENDING;?>">pending</option>
	<option value="<?php echo PIREP_ACCEPTED;?>">accepted</option>
	<option value="<?php echo PIREP_REJECTED;?>">rejected</option>
	
</select>
<input type="hidden" name="action" value="filter" />
<input type="submit" name="submit" value="filter" />
</form>
</div>
