<h3>Admin Activity Logs</h3>

<?php
if(!$all_logs)
{
	echo 'There is no activity';
	return;
}
?>

<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Date</th>
	<th>Name</th>
	<th>Message</th>
</tr>
</thead>
<tbody>
<?php

foreach($all_logs as $log)
{
	echo "<tr>
			<td width=\"5%\" nowrap>{$log->datestamp}</td>
			<td width=\"5%\" nowrap>".PilotData::getPilotCode($log->code, $log->pilotid)." - {$log->firstname} {$log->lastname}</td>
			<td>{$log->message}</td>
		  </tr>";
}
?>
</tbody>
</table>