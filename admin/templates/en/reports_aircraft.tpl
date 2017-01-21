<h3>Aircraft Reports</h3>
<?php
if(!is_array($acstats))
	$acstats = array();
?>
<?php
foreach($acstats as $stat)
{
?>
<h4><?php echo "{$stat->fullname} ({$stat->registration})"?></h4>
<table width="100px">
<tr>
	<td align="right"><strong>Total Flights:</strong></td>
	<td align="left"><?php echo $stat->routesflown == ' ' ? 0 : round($stat->routesflown, 2); ?><br /></td>
</tr>
<tr>
	<td align="right"><strong>Total Distance:</strong></td>
	<td align="left"><?php echo $stat->distance == '' ? 0 : round($stat->distance, 2); ?></td>
</tr>
<tr>
	<td align="right" nowrap=""><strong>Average Flight Distance:</strong></td>
	<td align="left"><?php echo $stat->averagedistance == '' ? 0 : round($stat->averagedistance, 2); ?></td>
</tr>
<tr>
	<td align="right"><strong>Total Hours:</strong></td>
	<td align="left"><?php echo $stat->totaltime == '' ? 0 : round($stat->totaltime, 2); ?></td>
</tr>
<tr>
	<td align="right"><strong>Average Flight Time:</strong></td>
	<td align="left"><?php echo $stat->averagetime == '' ? 0 : round($stat->averagetime, 2); ?></td>
</tr>
</table>
</p>
<?php	
}	
?>