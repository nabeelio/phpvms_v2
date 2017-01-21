<h2>Log for <?php echo $report->code.$report->pirepid?></h2>
<div style="overflow: scroll; height: 300px">
<?php
# Simple, each line of the log ends with *
# Just explode and loop.
$log = explode('*', $report->log);
foreach($log as $line)
{
	echo $line .'<br />';
}
?>
</div>