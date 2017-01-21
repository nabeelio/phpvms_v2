<h3>Site Settings</h3>
<p>Select available site options from this page. Don't forget to save!</p>
<form id="form" method="post" action="<?php echo SITE_URL?>/admin/action.php/settings/settings">

<table id="tabledlist" class="tablesorter">
<thead>
	<tr>
		<th>Setting Name</th>
		<th>Setting Value</th>
	</tr>
</thead>
<tbody>
<?php
	if(!$allsettings)
	{
		echo '<p>No settings have been added</p>';
	}
	else
	{
		foreach($allsettings as $setting)
		{
			
			echo '<tr>
					<td width="15%" nowrap>
						<strong>'.$setting->friendlyname.'</strong></td>
						<td>';
			
			switch($setting->name)
			{

				case 'PHPVMS_VERSION':
					echo PHPVMS_VERSION;
					break;
					
				case 'TOTAL_HOURS':
					
					echo $setting->value;
					
					break;
					
				case 'CURRENT_SKIN':
				
					$skins = SiteData::GetAvailableSkins();
					$skin = SettingsData::GetSetting('CURRENT_SKIN');
					
					echo '<SELECT name="CURRENT_SKIN">';
						
						$tot = count($skins);
						for($i=0;$i<$tot;$i++)
						{
							$sel = ($skin->value == $skins[$i])? 'selected' : '';
							echo '<option value="'.$skins[$i].'" '. $sel . '>'.$skins[$i].'</option>';
						}
   
					echo '</SELECT>';
					break;
					
				case 'DEFAULT_GROUP':
				
					$allgroups = PilotGroups::getAllGroups();
					$current = SettingsData::getSetting('DEFAULT_GROUP');
					
					echo '<SELECT name="DEFAULT_GROUP">';
						
						foreach($allgroups as $group)
						{
							$sel = ($current == $group->groupid)? 'selected' : '';
							echo '<option value="'.$group->groupid.'" '. $sel . '>'.$group->name.'</option>';
						}
   
					echo '</SELECT>';
					break;
				
					break;
					
				default:
					
					if($setting->value == 'true' || $setting->value == 'false')
					{
						if($setting->value == 'true')
						{
							$sel_true = 'selected';
							$sel_false = '';
						}
						else
						{
							$sel_true = '';
							$sel_false = 'selected';
						}
   
						echo '
								<SELECT name="' . $setting->name . '" onChange="showChanged();" >
								<option value="true" '. $sel_true . '>Enabled</option>
								<option value="false" ' . $sel_false . '>Disabled</option>
							  </SELECT>';
					}
					else
					{
						echo '<input type="text" name="'.$setting->name.'" value="'.$setting->value.'" />';
					}
					
					echo '<p>'.$setting->descrip.'</p>';
					break;
			}
			
			echo '</td></tr>';
		}
	}
?>
<tr>
	<td></td>
	<td><input type="hidden" name="action" value="savesettings">
		<input type="submit" name="submit" value="Save Settings" />
	</td>
</tr>
</tbody>
</table>
</form>