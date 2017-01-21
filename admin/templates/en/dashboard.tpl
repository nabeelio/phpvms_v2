<h3>VA Stats:</h3>
<table width="100%">
	<tr>
	<td valign="top" width="33%" nowrap="nowrap">		
		<strong>Users Online: </strong><?php echo count(StatsData::UsersOnline()); ?><br />
		<strong>Guests Online: </strong><?php echo count(StatsData::GuestsOnline()); ?>
	</td>
	<td valign="top" width="33%" nowrap="nowrap" >
		<strong>Total Pilots: </strong><?php echo StatsData::PilotCount(); ?><br />
		<strong>Total Flights: </strong><?php echo StatsData::TotalFlights(); ?><br />
		<strong>Total Hours Flown: </strong><?php echo StatsData::TotalHours(); ?>
	</td>
	<td valign="top" width="33%" nowrap="nowrap" >
		<strong>Miles Flown: </strong><?php echo StatsData::TotalMilesFlown(); ?><br />
		<strong>Total Schedules: </strong><?php echo StatsData::TotalSchedules(); ?><br />
		<strong>Flights Today: </strong><?php echo StatsData::TotalFlightsToday();?>
	</td>
	</tr>
</table>
<?php
MainController::Run('Dashboard', 'CheckInstallFolder');
echo $updateinfo;
?>
<h3>Pilot Reports for the Past Week</h3>
<div align="center" style="width=98%">
	<div id="reportcounts" align="center" width="400px" >
	<img src="<?php echo fileurl('/lib/images/loading.gif');?>" /><br /><br />
	Loading...
	</div>
</div>
<?php
if(Config::Get('VACENTRAL_ENABLED') == true && $unexported_count > 0)
{ ?>
	<h3>vaCentral Status: </h3>
	<p>You have <strong><?php echo $unexported_count?></strong> PIREPS waiting for export to vaCentral. 
	<a href="<?php echo adminurl('/vacentral/sendqueuedpireps'); ?>">Click here to send them</a> </p>
<?php
} ?>
<h3 style="margin-bottom: 0px;">Latest News</h3>
	<div style="overflow: auto; height: 400px; border: 1px solid #f5f5f5; margin-bottom: 20px; padding: 7px; padding-top: 0px; padding-bottom: 20px;">
	<?php echo $phpvms_news; ?>
	<p><a href="http://www.phpvms.net" target="_new">View All News</a></p>
	</div>
</td>
<?php
/*if(Config::Get('VACENTRAL_ENABLED') == true)
{
?>
<td valign="top" valign="50%">
	
	<h3 style="margin-bottom: 0px;">Latest vaCentral News</h3>
	<?php echo $vacentral_news; ?>
	<p><a href="http://www.vacentral.net" target="_new">View All News</a></p>
</td>
<?php
}*/
?>
<?php
/*
	Added in 2.0!
*/
$chart_width = '800';
$chart_height = '200';

/* Don't need to change anything below this here */
?>
<script type="text/javascript" src="<?php echo fileurl('/lib/js/ofc/js/swfobject.js')?>"></script>
<script type="text/javascript">
swfobject.embedSWF("<?php echo fileurl('/lib/js/ofc/open-flash-chart.swf');?>", 
	"reportcounts", "<?php echo $chart_width;?>", "<?php echo $chart_height;?>", 
	"9.0.0", "expressInstall.swf", 
	{"data-file":"<?php echo adminaction('/dashboard/pirepcounts');?>"});
</script>
