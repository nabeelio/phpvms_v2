<h3>Give Award</h3>
<?php
$allawards = AwardsData::GetAllAwards();

if(!$allawards)
{
	echo 'You have no added any awards!';
	return;
}
?>

<form id="addaward" method="POST" action="<?php echo adminaction('/pilotadmin/pilotawards');?>">
<select name="awardid">
<?php
foreach($allawards as $award)
{
	echo '<option value="'.$award->awardid.'">'.$award->name.'</option>';
}
?>
</select>
<input type="hidden" name="pilotid" value="<?php echo $pilotinfo->pilotid;?>" />
<input type="hidden" name="action" value="addaward" />
<input type="submit" name="submit" value="Add Award" />
</form>