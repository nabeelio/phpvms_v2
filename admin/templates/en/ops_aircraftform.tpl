<h3><?php echo $title;?></h3>
<p>* Denotes required fields</p>
<form action="<?php echo adminurl('/operations/aircraft');?>" method="post">
<dl>
<dt>* Aircraft ICAO Code</dt>
<dd><input name="icao" type="text" value="<?php echo $aircraft->icao; ?>" /></dd>

<dt>* Aircraft Name/Type (i.e B747-400)</dt>
<dd><input name="name" type="text" value="<?php echo $aircraft->name; ?>" /></dd>

<dt>* Full Name (Boeing 747-400 Combi)</dt>
<dd><input name="fullname" type="text"  value="<?php echo $aircraft->fullname; ?>" /></dd>

<dt>* Aircraft Registration</dt>
<dd><input name="registration" type="text"  value="<?php echo $aircraft->registration; ?>" />
	<p>TIP: Place an X in the registration to denote an inactive aircraft</p>
</dd>

<dt>* Maximum Passengers</dt>
<dd><input name="maxpax" type="text"  value="<?php echo $aircraft->maxpax; ?>" />
	<p>The maximum number of passengers that can be flown on this aircraft, for passenger or charter flights</p>
</dd>

<dt>* Maximum Cargo</dt>
<dd><input name="maxcargo" type="text"  value="<?php echo $aircraft->maxcargo; ?>" />
	<p>The maximum cargo load of this aircraft in <?php echo Config::Get('CARGO_UNITS'); ?>, for cargo flights</p>
</dd>

<dt>Link to download aircraft</dt>
<dd><input name="downloadlink" type="text"  value="<?php echo $aircraft->downloadlink; ?>" /></dd>

<dt>Link to aircraft image</dt>
<dd><input name="imagelink" type="text"  value="<?php echo $aircraft->imagelink; ?>" /></dd>

<dt></dt>
<dd>Some of this aircraft can be retrieved from <a href="http://www.airliners.net/aircraft-data/" target="_new">this site</a>.</dd>
<dt>Range</dt>
<dd><input name="range" type="text"   value="<?php echo $aircraft->range; ?>" /></dd>

<dt>Weight</dt>
<dd><input name="weight" type="text" value="<?php echo $aircraft->weight; ?>" /></dd>

<dt>Cruise</dt>
<dd><input name="cruise" type="text"  value="<?php echo $aircraft->cruise; ?>" /></dd>

<dt>Minimum Rank Required to fly</dt>
<dd>
	<select name="minrank">
		<option value="0" <?php if($aircraft->minrank == 0){ echo 'selected'; } ?>>None</option>
		<?php
		foreach($allranks as $rank)
		{
			if($aircraft->minrank == $rank->rankid)
				$sel = 'selected="selected"';
			else
				$sel = '';
				
			echo "<option value=\"{$rank->rankid}\" {$sel} >{$rank->rank}</option>";
		}
		?>
	</select>

</dd>

<dt>Enabled?</dt>
<?php $checked = ($aircraft->enabled==1 || !$aircraft)?'CHECKED':''; ?>
<dd><input type="checkbox" id="enabled" name="enabled" value="1" <?php echo $checked ?> /></dd>
	
<dt></dt>
<dd><input type="hidden" name="id" value="<?php echo $aircraft->id;?>" />
	<input type="hidden" name="action" value="<?php echo $action;?>" />
	<input type="submit" name="submit" value="<?php echo $title;?>" />
</dd>
</dl>
</form>