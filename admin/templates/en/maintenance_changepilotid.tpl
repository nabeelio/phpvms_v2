<p><b>NOTE:</b> Be VERY cautioned when changing a pilot ID. Even though this is 'controlled', problems can still occur. If you change the pilot ID to a number which is HIGHER than the highest pilot ID, all new registered pilots will have an ID starting after that. This WILL change their login ID. <b>This user cannot be logged in while doing the change.</b></p>
<p>The PILOT ID is shown in the following format below:<br />
<pre>FORMATTED_PILOT_ID - DATABASE_ID - NAME</pre>
You must enter a new DATABASE_ID. The formatted pilow ID is based on the database ID and the offset. Your current offset is <strong><?php echo Config::Get('PILOTID_OFFSET');?></strong>.</p>
<p>For example, you want the pilot ID to be VMS<?php echo Config::Get('PILOTID_OFFSET') + 200;?> - for the new ID you would enter: <?php echo 200 - intval(Config::Get('PILOTID_OFFSET'));?>. If your offset is 1000 (meaning all pilot IDs start from 1000), to have a pilot ID of 1030 you would change the DB ID to 30 (that's what you would enter below).
</p>
<p>
	<strong>ID Calculator:</strong> Enter the intended pilot ID: <input type="text" value="" onKeyUp="showid(this.value);" /><br />
	<span id="enterid"></span>
</p>
<hr>
<form action="" method="post">
<b>Select Pilot:</b><br />
<select name="old_pilotid">
<option value="0">Select a Pilot</option>
<?php 
	foreach($allpilots as $pilot)
	{
		if(isset($_POST['old_pilotid']))
		{
			if($_POST['old_pilotid'] === $pilot->id)
			{
				$selected = 'selected';
			}
			else
			{
				$selected = '';
			}
		}
		
		echo "<option value=\"{$pilot->pilotid}\" {$selected}>";
		echo PilotData::getPilotCode($pilot->code, $pilot->pilotid);
		echo ' - ' .$pilot->pilotid.' - '. $pilot->firstname.' '.$pilot->lastname;
		echo '</option>';		
	}
?>
</select>
<br /><br />
<b>Enter new ID - ONLY NUMERIC:</b><br />
<input type="text" name="new_pilotid" value="" />
<br />
<p><b>Are you sure?</b></p>
<input type="submit" name="submit" value="Confirm Change ID" />
</form>
<script type="text/javascript">
var OFFSET = <?php echo Config::Get('PILOTID_OFFSET')?>;
function showid(value)
{
	value = parseInt(value);
	if(value < OFFSET)
	{
		$("#enterid").html("The displayed pilot ID cannot be less than your offset");
	}
	
	id = value - OFFSET;
	$("#enterid").html("Enter \""+id+"\" in the field below (without quotes!)");
}
</script>