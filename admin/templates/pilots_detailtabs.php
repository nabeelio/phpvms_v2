<?php if(!defined('IN_PHPVMS') && IN_PHPVMS !== true) { die(); } ?>
<div id="wrapper">
<h3><?php echo $pilotinfo->firstname . ' ' . $pilotinfo->lastname; ?></h3>
<div id="dialogresult"></div>
<div id="tabcontainer" style="float: left; width: 100%">
	<ul>
    <?php if(PilotGroups::group_has_perm(Auth::$usergroups, EDIT_PILOTS)) { ?>
		<li><a href="#pilotdetails"><span>Pilot Details</span></a></li>
    <?php } if(PilotGroups::group_has_perm(Auth::$usergroups, EDIT_GROUPS)) { ?>    
		<li><a href="#pilotgroups" id="pilotgroupslink"><span>Pilot Groups</span></a></li>
    <?php } if(PilotGroups::group_has_perm(Auth::$usergroups, EDIT_AWARDS)) { ?>    
		<li><a href="#awards"><span>Pilot Awards</span></a></li>
    <?php } if(PilotGroups::group_has_perm(Auth::$usergroups, MODERATE_PIREPS)) { ?>    
		<li><a href="#pireps"><span>View PIREPs</span></a></li>
    <?php }if(PilotGroups::group_has_perm(Auth::$usergroups, FULL_ADMIN)) { ?>    
		<li><a href="#resetpass"><span>Pilot Options</span></a></li>
    <?php } ?>
	</ul>
<?php /** ======================================================== */ ?>
	<br />
    <?php if(PilotGroups::group_has_perm(Auth::$usergroups, EDIT_PILOTS)) { ?>
	<div id="pilotdetails">
    <?php
        Template::Show('pilots_details.tpl');
    ?>
	</div>
    <?php } if(PilotGroups::group_has_perm(Auth::$usergroups, EDIT_GROUPS)) { ?>    
	<div id="pilotgroups">
    <?php
        Template::Show('pilots_groups.tpl');
        Template::Show('pilots_addtogroup.tpl');
    ?>
	</div>
    <?php } if(PilotGroups::group_has_perm(Auth::$usergroups, EDIT_AWARDS)) { ?>    
	<div id="awards">
	<?php
        Template::Show('pilots_awards.tpl');
        Template::Show('pilots_addawards.tpl');
	?>
	</div>
    <?php } if(PilotGroups::group_has_perm(Auth::$usergroups, MODERATE_PIREPS)) { ?>    
	<div id="pireps">
	<?php
        Template::Show('pireps_list.tpl');
    ?>
	</div>
    <?php } if(PilotGroups::group_has_perm(Auth::$usergroups, FULL_ADMIN)) { ?>    
	<div id="resetpass">
    <?php
        Template::Show('pilots_options.tpl');
    ?>
	</div>
    <?php } ?>    
</div>
</div>

<script type="text/javascript">
$("#tabcontainer").tabs();
/*
$("#pilotgroupslink").bind('click', function(e) {
    $.get("<?php echo adminaction('/pilotadmin/pilotgrouptab/'.$pilotinfo->pilotid);?>", function(d){
        $("#pilotgroups").html(d);
    })    
})
*/
</script>