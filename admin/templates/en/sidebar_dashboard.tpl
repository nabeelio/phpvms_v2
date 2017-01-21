<h3>Admin Panel</h3>
<p>Welcome back, <?php echo Auth::$userinfo->firstname?>!</p>

<h3>Latest Stats</h3>
<p>
<?php
if(PilotGroups::group_has_perm(Auth::$usergroups, MODERATE_PIREPS)) 
{
?>
<strong><a href="<?php echo SITE_URL?>/admin/index.php/pirepadmin/viewpending"><?php echo  count(PIREPData::GetAllReportsByAccept(PIREP_PENDING))?></strong> PIREPs pending</a><br />
<?php 
}
if(PilotGroups::group_has_perm(Auth::$usergroups, MODERATE_REGISTRATIONS)) 
{
?>
<strong><a href="<?php echo SITE_URL?>/admin/index.php/pilotadmin/pendingpilots"><?php echo  count(PilotData::GetPendingPilots())?></strong> Pilot registrations pending</a>
</p>
<?php
}
?>