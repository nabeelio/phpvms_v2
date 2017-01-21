<h3>Reports</h3>

<h4>VA Stats: At a Glance</h4>
<div class="outlined">
	<strong>Total Pilots: </strong><?php echo StatsData::PilotCount(); ?><br />
	<strong>Total Flights: </strong><?php echo StatsData::TotalFlights(); ?><br />
	<strong>Total Hours Flown: </strong><?php echo StatsData::TotalHours(); ?><br />
</div>

<?php
foreach($allairlines as $airline)
{
?>

<h4>Stats for <?php echo $airline->name?></h4>
<div class="outlined">
	<strong>Total Pilots: </strong><?php echo StatsData::PilotCount($airline->code); ?><br />
	<strong>Total Flights: </strong><?php echo StatsData::TotalFlights($airline->code); ?><br />
	<strong>Total Hours Flown: </strong><?php echo StatsData::TotalHours($airline->code); ?><br />
</div>

<?php
}


if(is_array($acstats))
{
	?>
<h4>Aircraft Usage<span> Aircraft Hours and Usage</span></h4>
<table id="tabledlist" class="tablesorter">
	<thead>
		<tr>
			<th>Aircraft</th>
			<th>Hours</th>
			<th>Miles</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($acstats as $stat)
		{
		?>
			<tr>
				<td><?php echo "$stat->aircraft ($stat->registration)"?></td>
				<td><?php echo $stat->totaltime?></td>
				<td><?php echo $stat->distance?></td>
			</tr>
					
		<?php	
		}?>
	</tbody>
</table>
<?php } ?>

<?php
if(is_array($toproutes)) {
	?>
<h4>Routes Top 10 Routes</h4>

<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Route</th>
	<th>Times Flown</th>
	
</tr>
</thead>
<tbody>
	<?php
	foreach($toproutes as $route)
	{
		?>
		<tr>
		<td><?php echo $route->code.$route->flightnum." ($route->depicao to $route->arricao)"?>
		</td>
		<td valign="top"><strong><?php echo $route->timesflown?></strong>
			<span class="flownchart" style="margin-top: 15px; margin-left: 4px;">
				<?php 
				#$tot = $route->timesflown*10;
				#echo ($tot+10).', '.$tot.','.($tot-2)
				?>
			</span>
		</td>
		</tr>
	<?php
	}?>
</tbody>
</table>
<?php } ?>