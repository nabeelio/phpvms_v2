<?php
if(!$reports)
{
	echo 'No reports have been filed';
	return;
}

foreach($reports as $report)
{
?>
<p><a href="<?php echo url('/pireps/viewreport/'.$report->pirepid);?>">#<?php echo $report->pirepid
	. ' - ' . $report->code.$report->flightnum?></a> - <a href="<?php echo url('/profile/view/'.$report->pilotid);?>"><?php echo $report->firstname . ' ' . $report->lastname?></a></p>
<?php
}
?>