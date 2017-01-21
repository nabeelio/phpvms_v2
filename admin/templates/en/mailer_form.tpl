<h3>Mass Mailer</h3>
<form method="post" action="<?php echo adminurl('/massmailer/sendmail');?>">
<p>
	<strong>Subject: </strong> <input type="text" name="subject" value="" />
</p>
<p>
	<strong>Message:</strong>
</p>
<p>
	<textarea name="message" id="editor" style="width: 600px; height: 250px;">To: {PILOT_FNAME} {PILOT_LNAME}, </textarea>
</p>
<p>Select groups to send to:<br />
<?php $total = StatsData::PilotCount(); ?>
<input type="checkbox" name="groups[]" value="all" />All Pilots (<?php echo $total?> pilots)<br />
<?php
foreach($allgroups as $group)
{
	$total = count(PilotGroups::getUsersInGroup($group->groupid));
	echo "<input type=\"checkbox\" name=\"groups[]\" value=\"{$group->groupid}\" />{$group->name} - ({$total} pilots)<br />";
}
?>

</p>
<p>
	<input type="submit" name="submit" value="Send Email" />
</p>
</form>