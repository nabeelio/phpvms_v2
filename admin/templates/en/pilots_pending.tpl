<h3>Pending Pilots</h3>
<?php
if(!$allpilots)
{
	echo '<p>There are no pilots!</p>';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Pilot Name</th>
	<th>Email Address</th>
	<th>Location</th>
	<th>Hub</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php
foreach($allpilots as $pilot)
{
?>
<tr>
	<td><a href="<?php echo SITE_URL?>/admin/index.php/pilotadmin/viewpilots?action=viewoptions&pilotid=<?php echo $pilot->pilotid;?>"><?php echo $pilot->firstname . ' ' . $pilot->lastname; ?></a></td>
	<td align="center"><?php echo $pilot->email; ?></td>
	<td align="center"><?php echo $pilot->location; ?></td>
	<td align="center"><?php echo $pilot->hub; ?></td>
	<td align="center" width="1%" nowrap>
        <button href="<?php echo SITE_URL?>/admin/action.php/pilotadmin/pendingpilots" action="approvepilot"
			id="<?php echo $pilot->pilotid;?>" class="ajaxcall {button:{icons:{primary:'ui-icon-circle-check'}}}">Accept</button>
				
        <button href="<?php echo SITE_URL?>/admin/action.php/pilotadmin/pendingpilots" action="rejectpilot"
			id="<?php echo $pilot->pilotid;?>" class="ajaxcall {button:{icons:{primary:'ui-icon-circle-close'}}}">Reject</button>
	</td>
</tr>
<?php
}
?>
</tbody>
</table>