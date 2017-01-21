<h3>Edit Profile</h3>
<form action="<?php echo url('/profile');?>" method="post" enctype="multipart/form-data">
<dl>
	<dt>Name</dt>
	<dd><?php echo $userinfo->firstname . ' ' . $userinfo->lastname;?></dd>
	
	<dt>Airline</dt>
	<dd><?php echo $userinfo->code?>
		<p>To request a change, contact your admin</p>
	</dd>
	
	<dt>Email Address</dt>
	<dd><input type="text" name="email" value="<?php echo $userinfo->email;?>" />
		<?php
			if(isset($email_error) && $email_error == true)
				echo '<p class="error">Please enter your email address</p>';
		?>
	</dd>
	
	<dt>Location</dt>
	<dd><select name="location">
		<?php
		foreach($countries as $countryCode=>$countryName)
		{
			if($userinfo->location == $countryCode)
				$sel = 'selected="selected"';
			else	
				$sel = '';
			
			echo '<option value="'.$countryCode.'" '.$sel.'>'.$countryName.'</option>';
		}
		?>
		</select>
		<?php
			if(isset($location_error) &&  $location_error == true)
				echo '<p class="error">Please enter your location</p>';
		?>
	</dd>
	
	<dt>Language</dt>
	<dd><select name="language">
		<?php
		foreach($languages as $language)
		{
			if($userinfo->language == $language->language)
				$sel = 'selected="selected"';
			else
				$sel = '';
			
			echo '<option value="'.$language->language.'" '.$sel.'>'.$language->name.'</option>';
		}
		?>
	</select>
	</dd>
	
	<dt>Signature Background</dt>
	<dd><select name="bgimage">
		<?php
		foreach($bgimages as $image)
		{
			if($userinfo->bgimage == $image)
				$sel = 'selected="selected"';
			else	
				$sel = '';
			
			echo '<option value="'.$image.'" '.$sel.'>'.$image.'</option>';
		}
		?>
		</select>
	</dd>
	
	<?php
	if($customfields)
	{
		foreach($customfields as $field)
		{
			echo '<dt>'.$field->title.'</dt>
				  <dd>';
			
			if($field->type == 'dropdown')
			{
				$field_values = SettingsData::GetField($field->fieldid);				
				$values = explode(',', $field_values->value);
				
				
				echo "<select name=\"{$field->fieldname}\">";
			
				if(is_array($values))
				{						
					foreach($values as $val)
					{
						$val = trim($val);
						
						if($val == $field->value)
							$sel = " selected ";
						else
							$sel = '';
						
						echo "<option value=\"{$val}\" {$sel}>{$val}</option>";
					}
				}
				
				echo '</select>';
			}
			elseif($field->type == 'textarea')
			{
				echo '<textarea class="customfield_textarea"></textarea>';
			}
			else
			{
				echo '<input type="text" name="'.$field->fieldname.'" value="'.$field->value.'" />';
			}
			
			echo '</dd>';
		}
	}
	?>
	
	<dt>Avatar:</dt>
	<dd><input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::Get('AVATAR_FILE_SIZE');?>" />
		<input type="file" name="avatar" size="40"> 
		<p>Your image will be resized to <?php echo Config::Get('AVATAR_MAX_HEIGHT').'x'.Config::Get('AVATAR_MAX_WIDTH');?>px</p>
	</dd>
	<dt>Current Avatar:</dt>
	<dd><?php	
			if(!file_exists(SITE_ROOT.AVATAR_PATH.'/'.$pilotcode.'.png'))
			{
				echo 'None selected';
			}
			else
			{
		?>
			<img src="<?php	echo SITE_URL.AVATAR_PATH.'/'.$pilotcode.'.png';?>" /></dd>
		<?php
		}
		?>
	<dt></dt>
	<dd><input type="hidden" name="action" value="saveprofile" />
		<input type="submit" name="submit" value="Save Changes" /></dd>
</dl>
</form>