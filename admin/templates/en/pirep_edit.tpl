<h3>Edit a flight report</h3>
<?php
if(isset($message))
	echo '<div id="error">'.$message.'</div>';
?>
<form action="<?php echo SITE_URL?>/admin/index.php/pirepadmin/viewpending" method="post">
<table width="100%" class="tablesorter">
<tr><td colspan="2" style="border: none;"><h4>PIREP Basics</h4></td></tr>
<tr>
	<td style="font-weight: bold;">Pilot:</td>
	<td><strong><?php echo $pirep->firstname . ' ' . $pirep->lastname;?></strong></td>
</tr>
<tr>
	<td style="font-weight: bold;">Select Airline:</td>
	<td>
		<select name="code" id="code">
			<option value="">Select your airline</option>
			<?php
			foreach($allairlines as $airline)
			{
				$sel = ($pirep->code == $airline->code)?'selected':'';
					
				echo '<option value="'.$airline->code.'" '.$sel.'>'.$airline->name.'</option>';
			}
			?>
		</select>
	</td>
</tr>
<tr>	
	<td style="font-weight: bold;">Enter Flight Number:</td>
	<td><input type="text" name="flightnum" value="<?php echo $pirep->flightnum ?>" /></td>
</tr>
<tr>	
	<td style="font-weight: bold;">Select Departure Airport:</td>
	<td>
		<div id="depairport">
		<input name="depicao" class="airport_select" value="<?php echo $pirep->depicao;?>" onclick="" />
		</div>
	</td>
</tr>
<tr>	
	<td style="font-weight: bold;">Select Arrival Airport:</td>
	<td>
		<div id="arrairport">
		<input name="arricao" class="airport_select" value="<?php echo $pirep->arricao;?>" onclick="" />
		</div>
	</td>
</tr>
<tr>	
	<td style="font-weight: bold;">Select Aircraft:</td>
	<td>
		<select name="aircraft" id="aircraft">
			<option value="">Select the aircraft of this flight</option>
		<?php
		
		foreach($allaircraft as $aircraft)
		{
			$sel = ($pirep->aircraftid == $aircraft->id) ? 'selected':'';
			echo '<option value="'.$aircraft->id.'" '.$sel.'>'.$aircraft->name.' - '.$aircraft->registration.'</option>';
		}
		?>
		</select>
		<?php
		if($pirep->aircraftid == '')
		{
			Template::Set('message', 'You must set an aircraft');
			Template::Show('core_error.tpl');
		}
		?>
	</td>
</tr>

<tr>		
	<td style="font-weight: bold;">Flight Time</td>
	<td><input type="text" name="flighttime" value="<?php echo $pirep->flighttime; ?>" />
		<p>Enter as hours:minutes - "5:30" is five hours and thirty minutes</p></td>
</tr>

<?php
// List all of the custom PIREP fields
if(!$pirepfields) $pirepfields = array();

if(count($pirepfields) > 0)
{
	echo '<tr><td colspan="2" style="border: none;"><h4>Custom Fields</h4></td></tr>';
}

foreach($pirepfields as $field)
{
?>
	<tr>
	<td style="font-weight: bold;"><?php echo $field->title ?></td>
	<td>
	<?php
	
	// Determine field by the type
	$value = PIREPData::GetFieldValue($field->fieldid, $pirep->pirepid);

	if($field->type == '' || $field->type == 'text')
	{
	?>
		<input type="text" name="<?php echo $field->name ?>" value="<?php echo $value ?>" />
	<?php
	} 
	elseif($field->type == 'textarea')
	{
		echo '<textarea name="'.$field->name.'">'.$value.'</textarea>';
	}
	elseif($field->type == 'dropdown')
	{
		$values = explode(',', $field->options);
		
		echo '<select name="'.$field->name.'">';
		foreach($values as $fvalue)
		{
			if($value == $fvalue)
			{
				$sel = 'selected="selected"';
			}
			else	
			{
				$sel = '';
			}
			
			$value = trim($fvalue);
			echo '<option value="'.$fvalue.'" '.$sel.'>'.$fvalue.'</option>';
		}
		echo '</select>';		
	}
	?>
	
	</td>
	</tr>
<?php
}
?>
<tr><td colspan="2" style="border: none;"><h4>Finances</h4></td></tr>
<tr>	
	<td style="font-weight: bold;">Load</td>
	<td><input type="text" name="load" value="<?php echo $pirep->load; ?>" />
		<p>This is the load of this flight. It's automatically determined, though you can adjust it here</p></td>
</tr>
<tr>		
	<td style="font-weight: bold;">Price</td>
	<td><input type="text" name="price" value="<?php echo $pirep->price; ?>" />
		<p>This is the price per load unit for this flight.</p></td>
</tr>
<tr>	
	<td style="font-weight: bold;">Fuel Used</td>
	<td><input type="text" name="fuelused" value="<?php echo $pirep->fuelused; ?>" />
		<p>This is the fuel used on this flight in <?php echo Config::Get('LIQUID_UNIT_NAMES', Config::Get('LiquidUnit'))?></p></td>	
</tr>
<tr>		
	<td style="font-weight: bold;">Fuel Price</td>
	<td><input type="text" name="fuelunitcost" value="<?php echo $pirep->fuelunitcost?>" />
		<p>This is the price of fuel, <?php echo Config::Get('MONEY_UNIT').' per '.Config::Get('LIQUID_UNIT_NAMES', Config::Get('LiquidUnit'))?>. If you change this, the total fuel cost amount below will be calculated and replaced</p></td>
</tr>
<tr>		
	<td style="font-weight: bold;">Gross Revenue:</td>
	<td><?php echo FinanceData::FormatMoney($pirep->revenue); ?>
		<p>Change the load and price variables above to adjust this value.</p></td>
</tr>
<tr>	
	<td style="font-weight: bold;">Total fuel cost</td>
	<td><?php echo FinanceData::FormatMoney($pirep->fuelprice); ?> 
		<p>Change the fuel cost above to edit this total price</p></td>
</tr>
<tr>		
	<td style="font-weight: bold;">Expenses</td>
	<td><input type="text" name="expenses" value="<?php echo $pirep->expenses?>" />
		<p>Additional expenses for this flight (catering, cleaning, etc)</p></td>
</tr>
<tr>		
	<td style="font-weight: bold;">Pilot Pay</td>
	<td><input type="text" name="pilotpay" value="<?php echo $pirep->pilotpay;?>" />
		<p>This is what the pilot will be paid, per hour, for this flight</p></td>
</tr>
<tr>		
	<td style="font-weight: bold;">Total Revenue for flight:</td>
	<td><?php echo FinanceData::FormatMoney($pirep->revenue); ?> 
		<p></p></td>
</tr>

<tr><td colspan="2" style="border: none;"><h4>Comments</h4></td></tr>
<tr>		
	<td>Comments</td>
	<td>
		<?php
		if(!$comments)
		{
			echo '<p>No comments</p>';
			$comments=array();
		}
		
		foreach($comments as $comment)
		{?>
			<p><?php echo $comment->comment; ?><br />
				<strong>By <?php echo $comment->firstname.' '.$comment->lastname ?></strong></p>
		<?php
		}
		?>
		
		<hr>
		<strong>Add Comment:</strong><br />
		<textarea name="comment" style="width: 50%; height: 150px"></textarea>
	</td>
</tr>
	<?php
	if($pirep->log != '')
	{
	?>
	<tr><td colspan="2" style="border: none;"><h4>Log File</h4></td></tr>
	<tr>
	<td>Log File:</td>
	<td>
		<a href="#" onclick="$('#log').toggle(); return false;">View Log</a></li>
		<div id="log" style="display: none; overflow: auto; height: 400px; border: 1px solid #666; margin-bottom: 20px; padding: 5px; padding-top: 0px; padding-bottom: 20px;">
		<?php
		# Simple, each line of the log ends with *
		# Just explode and loop.
		$log = explode('*', $pirep->log);
		foreach($log as $line)
		{
			echo $line .'<br />';
		}
		?>
		</div>
			
	</td>
	</tr>
	<?php
	}
	?>
</tr>
<tr>
<td></td>
<td><input type="hidden" name="pirepid" value="<?php echo $pirep->pirepid;?>" />
	<input type="hidden" name="action" value="editpirep" />
	<input type="submit" name="submit_pirep" value="Save PIREP" />
	<input type="submit" name="submit_pirep" value="Accept PIREP" />
	<input type="submit" name="submit_pirep" value="Reject PIREP" />
</td>
</tr>
</table>
</form>
<script type="text/javascript">
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
	delay: 0,
	minLength: 2
});
</script>