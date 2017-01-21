<div id="wrapper">
<h3><?php echo $title?></h3>
<form action="<?php echo adminurl('/operations/schedules');?>" method="post">
<table width="100%" class="tablesorter">
<tr>
	<td valign="top"><strong>Code: </strong></td>
	<td>
		<select name="code">
		<?php
		
		foreach($allairlines as $airline)
		{
			if($airline->code == $schedule->code)
				$sel = 'selected';
			else
				$sel = '';
	
			echo '<option value="'.$airline->code.'" '.$sel.'>'.$airline->code.' - '.$airline->name.'</option>';
		}
		?>
		</select>
	</td>
</tr>
<tr>
	<td><strong>Flight Number:</strong></td>
	<td>
		<input type="text" name="flightnum" value="<?php echo $schedule->flightnum;?>" />
	</td>
</tr>
<tr>
	<td width="3%" nowrap><strong>Departure Airport:</strong></td>
	<td><input id="depicao" name="depicao" class="airport_select" value="<?php echo $schedule->depicao;?>" onclick="" />
	</td>
</tr>
<tr>
	<td><strong>Arrival Airport:</strong></td>
	<td><input id="arricao" name="arricao" class="airport_select" value="<?php echo $schedule->arricao;?>" onclick="" />
	</td>
</tr>
<tr>
	<td valign="top"><strong>Departure Time:</strong> </td>
	<td><input type="text" name="deptime" value="<?php echo $schedule->deptime?>" />
		<p>Please enter time as: HH::MM Timezone (eg: 17:30 EST, or 5:30 PM EST)</p>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Arrival Time:</strong> </td>
	<td><input type="text" name="arrtime" value="<?php echo $schedule->arrtime?>" />
		<p>Please enter time as: HH::MM Timezone (eg: 17:30 EST, or 5:30 PM EST)</p>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Days of Week: </strong></td>
	<td>
		<?php
		/*$days = array('Sunday', 'Monday', 'Tuesday',
					  'Wednesday', 'Thursday', 'Friday',
					  'Saturday');
		*/
		
		$days = Config::Get('DAYS_LONG');
		
		for($i=0; $i<=6; $i++)
		{
			# Add blank string to typecast from int to string, otherwise it won't search
			if(strpos($schedule->daysofweek, $i.'') === false)
				$checked = '';
			else
				$checked = 'checked';
			
			echo '<input type="checkbox" name="daysofweek[]" value="'.$i.'" '.$checked.'>'.$days[$i].'  ';			
		}
		?>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Distance:</strong> </td>
	<td><input type="text" name="distance" id="distance" value="<?php echo $schedule->distance?>" />
		<p><a href="#" onclick="calcDistance(); return false;">Calculate Distance</a>. Leaving blank or 0 (zero) will automatically calculate the distance.</p></td>
</tr>
<tr>
	<td valign="top"><strong>Flight Time:</strong> </td>
	<td><input type="text" name="flighttime" value="<?php echo $schedule->flighttime?>" />
	<p>Please enter as HH:MM</p>
	</td>
</tr>
<tr>
	<td><strong>Equipment: </strong></td>
	<td><select name="aircraft">
		<?php
		
		foreach($allaircraft as $aircraft)
		{
			if($aircraft->registration == $schedule->registration)
				$sel = 'selected';
			else
				$sel = '';
	
			echo '<option value="'.$aircraft->id.'" '.$sel.'>'.$aircraft->name.' ('.$aircraft->registration.')</option>';
		} ?>
		</select>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Flight Level:</strong></td>
	<td><input type="text" name="flightlevel" value="<?php echo $schedule->flightlevel?>" />
	<p>Please enter as a full-numeric altitude. Should be in feet, to remain accurate with any ACARS.</p>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Flight Type</strong></td>
	<td><select name="flighttype">
			<?php
			foreach($flighttypes as $flightkey=>$flighttype)
			{
				if($schedule->flighttype == $flightkey)
					$sel = 'selected';
				else	
					$sel = '';
			?>
				<option value="<?php echo $flightkey?>" <?php echo $sel; ?>><?php echo $flighttype?> Flight</option>
			<?php
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Route (optional)</strong></td>
	<td><textarea name="route" style="width: 60%; height: 75px" id="route"><?php echo $schedule->route?></textarea>
		<p><a id="dialog" class="preview"
			href="<?php echo SITE_URL?>/admin/action.php/operations/viewmap?type=preview">View Route</a></p>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Price</strong> </td>
	<td><input type="text" name="price" value="<?php echo $schedule->price?>" />
		<p>This is the ticket price, or price per <?php echo Config::Get('CARGO_UNITS'); ?>
			for a cargo flight.</p>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Notes</strong></td>
	<td valign="top" style="padding-top: 0px">
		Any notes about the flight, including frequency, dates flown, etc.<br /><br />
		<textarea name="notes" style="width: 60%; height: 150px"><?php echo $schedule->notes?></textarea>
	</td>
</tr>
<tr>
	<td valign="top"><strong>Enabled?</strong></td>
	<?php $checked = ($schedule->enabled==1 || !$schedule)?'CHECKED':''; ?>
	<td><input type="checkbox" id="enabled" name="enabled" <?php echo $checked ?> /></td>
	</td>
</tr>
<tr>
	<td></td>
	<td><input type="hidden" name="action" value="<?php echo $action?>" />
		<input type="hidden" name="id" value="<?php echo $schedule->id?>" />
		<input type="submit" name="submit" value="<?php echo $title?>" />
	</td>
</tr>
</table>
</form>
</div>
<script type="text/javascript">
$(".preview").click(function()
{
	depicao=$("#depicao").val();
	arricao=$("#arricao").val();
	route=escape($("#route").val());
	
	url = this.href
		+"&depicao="+depicao
		+"&arricao="+arricao
		+"&route="+route;
			
	$('#jqmdialog').jqm({ajax: url}).jqmShow();
	
	return false;
});

<?php
$airport_list = array();
foreach($allairports as $airport)
{
	$airport->name = addslashes($airport->name);
	$airport_list[] = "{label:\"{$airport->icao} ({$airport->name})\", value: \"{$airport->icao}\"}";
}
$airport_list = implode(',', $airport_list);
?>
var airport_list = [<?php echo $airport_list; ?>];
$(".airport_select").autocomplete({
	source: airport_list,
	minLength: 2,
	delay: 0
});

</script>