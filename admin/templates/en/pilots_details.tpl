<form id="dialogform" action="<?php echo adminaction('/pilotadmin/viewpilots');?>" method="post">
<table id="tabledlist" class="tablesorter" style="float: left">
<thead>
	<tr>
		<th colspan="2">Edit Pilot Details</th>	
	</tr>
</thead>
<tbody>

<tr>
	<td>Avatar</td>
	<td>
		<?php
		$pilotcode = PilotData::GetPilotCode($pilotinfo->code, $pilotinfo->pilotid);

		if(!file_exists(SITE_ROOT.AVATAR_PATH.'/'.$pilotcode.'.png'))
		{
			echo 'None selected';
		}
		else
		{
		?>
			<img src="<?php	echo SITE_URL.AVATAR_PATH.'/'.$pilotcode.'.png';?>" />
		<?php
		}
		?>
	</td>

</tr>

<tr>
	<td>Pilot ID</td>
	<td><?php echo $pilotcode ?></td>
</tr>

<tr>
	<td>First Name</td>
	<td><input type="text" name="firstname" value="<?php echo $pilotinfo->firstname;?>" /></td>
</tr>

<tr>
	<td>Last Name</td>
	<td><input type="text" name="lastname" value="<?php echo $pilotinfo->lastname;?>" /></td>
</tr>
<tr>
	<td>Email Address</td>
	<td><input type="text" name="email" value="<?php echo $pilotinfo->email;?>" /></td>
</tr>
<tr>
	<td>Airline</td>
	<td>
		<select name="code">
		<?php
		$allairlines = OperationsData::GetAllAirlines();
		foreach($allairlines as $airline)
		{
			if($pilotinfo->code == $airline->code)
				$sel =  ' selected';
			else
				$sel = '';
				
			echo '<option value="'.$airline->code.'" '
						.$sel.'>'.$airline->name.'</option>';
		}
		?>	
		</select>
	</td>
</tr>
<tr>
	<td>Location</td>
	<td><select name="location">
			<?php
			foreach($countries as $countryCode=>$countryName)
			{
				if($pilotinfo->location == $countryCode)
					$sel = 'selected="selected"';
				else	
					$sel = '';
				
				echo '<option value="'.$countryCode.'" '.$sel.'>'.$countryName.'</option>';
			}
					?>
		</select>
	</td>
</tr>
<tr>
	<td>Hub</td>
	<td>
		<select name="hub">
		<?php
		$allhubs = OperationsData::GetAllHubs();
		foreach($allhubs as $hub)
		{
			if($pilotinfo->hub == $hub->icao)
				$sel = ' selected';
			else
				$sel = '';
			
			echo '<option value="'.$hub->icao.'" '.$sel.'>'.$hub->icao.' - ' . $hub->name .'</option>';
		}
		?>	
		</select>
	</td>
</tr>
<tr>
	<td>Current Rank</td>
	<td>
	<?php
	if(Config::Get('RANKS_AUTOCALCULATE') == false)
	{
		$allranks = RanksData::GetAllRanks();
		echo '<select name="rank">';
		
		foreach($allranks as $rank)
		{
			echo "<option value=\"{$rank->rankid}\"";
			if($pilotinfo->rankid == $rank->rankid) echo " selected";
			echo ">{$rank->rank}</option>";
		}
		echo '</select>';
	}
	else
	{
		echo $pilotinfo->rank;
	}
	?></td>
</tr>
<tr>
	<td>Date Joined</td>
	<td><?php echo date(DATE_FORMAT, strtotime($pilotinfo->joindate));?></td>
</tr>
<tr>
	<td>Last Login</td>
	<td><?php echo date(DATE_FORMAT, strtotime($pilotinfo->lastlogin));?></td>
</tr>
<tr>
	<td>Last Flight</td>
	<td><?php echo date(DATE_FORMAT, strtotime($pilotinfo->lastpirep));?></td>
</tr>
<tr>
	<td>Total Flights</td>
	<td><input type="text" name="totalflights" value="<?php echo $pilotinfo->totalflights;?>" /></td>
</tr>
<tr>
	<td>Total Hours</td>
	<td><?php echo $pilotinfo->totalhours;?>
		<input type="hidden" name="totalhours" value="<?php echo $pilotinfo->totalhours;?>" />
	</td>
</tr>
<tr>
	<td>Transfer Hours</td>
	<td><input type="text" name="transferhours" value="<?php echo $pilotinfo->transferhours;?>" /></td>
</tr>
<tr>
	<td>Total Pay</td>
	<td><input type="text" name="totalpay" value="<?php echo $pilotinfo->totalpay;?>" /></td>
</tr>
<tr>
	<td>Pilot active?</td>
	<td><?php 
		if(intval($pilotinfo->retired) == 1) 
		{  
			$retsel='selected'; 
			$activesel = ''; 
		}
		else
		{
			$activesel = 'selected'; 
			$retsel = '';
		}
		?>
		<select name="retired">
			<option value="0" <?php echo $activesel?>>Active</option>
			<option value="1" <?php echo $retsel?>>Inactive</option>
		</select>
	
	</td>
</tr>
<?php
if($customfields)
{
	foreach($customfields as $field)
	{
?>
	<tr>
		<td><?php echo $field->title;?></td>
		<td>
		<?php
		if($field->type == 'dropdown')
		{
			echo "<select name=\"{$field->fieldname}\">";
			$values = explode(',', $field->fieldvalues);
		
			if(is_array($values))
			{						
				foreach($values as $val)
				{
					$sel = ($field->value === $val) ? 'sel="selected"' : '';
					
					$val = trim($val);
					echo "<option value=\"{$val}\" {$sel} >{$val}</option>";
				}
			}
			
			echo '</select>';
		}
		elseif($field->type == 'textarea')
		{
			echo '<textarea name="'.$field->fieldname.'" style="width: 400px; height: 100px" class="customfield_textarea">'.$field->value.'</textarea>';
		}
		else
		{
			echo '<input type="text" name="'.$field->fieldname.'" value="'.$field->value.'" />';
		}
		?>
		</td>
	</tr>
<?php
	}
}
?>	
	<tr>
		<td colspan="2">
			<input type="hidden" name="pilotid" value="<?php echo $pilotinfo->pilotid;?>" />
			<input type="hidden" name="action" value="saveprofile" />
			<input type="submit" name="submit" value="Save Changes" />
			<div id="results"></div>
		</td>
	</tr>
</tbody>
</table>
</form>