<?php
if(PilotGroups::group_has_perm(Auth::$usergroups, FULL_ADMIN)) 
{
$pilotid = $_GET['pilotid'];
?>
<h3>Reset Pilot Password</h3>
<p>If someone has forgetten their password and it needs to be reset. </p>
<form id="pilotoptionchangepass" action="<?php echo SITE_URL?>/admin/action.php/pilotadmin/viewpilots" method="post">
<dl>
	<dt>Enter new password</dt>
	<dd><input type="password" name="password1" /></dd>
	
	<dt>Enter password again</dt>
	<dd><input type="password" name="password2" /></dd>
	
	<dt></dt>
	<dd><input type="hidden" name="pilotid" value="<?php echo $pilotid;?>" />
		<input type="hidden" name="action" value="changepassword" />
		<input type="submit" name="submit" value="Change Password" /></dd>
</dl>
</form>
<?php
if($pilotid != Auth::$userinfo->pilotid)
{?>
<h3>Delete Pilot</h3>
<p><strong>Warning!</strong> This is NOT reversible. This removes all of this pilot's information and data,
	including PIREPS and their registration.</p>
<form id="deletepilot" action="<?php echo SITE_URL?>/admin/index.php/pilotadmin/viewpilots" method="post">
<dl>	
	<dt></dt>
	<dd><input type="hidden" name="pilotid" value="<?php echo $pilotid;?>" />
		<input type="hidden" name="action" value="deletepilot" />
		<input type="submit" name="submit" onclick="return doublecheck();" value="Delete Pilot" /></dd>
</dl>
</form>
<?php
}
}
?>
<script type="text/javascript">
function doublecheck()
{
	var answer = confirm("Are you sure you want to delete?")
	if (answer) {
		return true;
	}
	
	return false;
}
</script>