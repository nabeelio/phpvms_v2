<h3><?php echo $title?></h3>
<form id="flashForm" action="<?php echo adminaction('/operations/airports');?>" method="post">
<dl>
<dt>Airport ICAO Code *</dt>
<dd><input id="airporticao" name="icao" type="text" value="<?php echo $airport->icao?>" /> 
	<button id="lookupicao" onclick="lookupICAO(); return false;">Look Up</button>
</dd>

<dt></dt>
<dd><div id="statusbox"></div></dd>
<dt>Airport Name *</dt>
<dd><input id="airportname" name="name" type="text" value="<?php echo $airport->name?>" /></dd>

<dt>Country Name *</dt>
<dd><input id="airportcountry" name="country" type="text" value="<?php echo $airport->country?>"  /></dd>

<dt>Latitude *</dt>
<dd><input id="airportlat" name="lat" type="text" value="<?php echo $airport->lat?>" /></dd>

<dt>Longitude *</dt>
<dd><input id="airportlong" name="lng" type="text" value="<?php echo $airport->lng?>" /></dd>

<dt>Chart Link</dt>
<dd><input id="chartlink" name="chartlink" type="text" value="<?php echo $airport->chartlink?>" /></dd>

<dt>Fuel Price *</dt>
<dd><input id="fuelprice" name="fuelprice" type="text" value="<?php echo $airport->fuelprice?>" />
<p>This is the price per <?php echo Config::Get('LIQUID_UNIT_NAMES', Config::Get('LiquidUnit'))?>. Leave blank or 0 (zero) to use the default value of <?php echo Config::Get('FUEL_DEFAULT_PRICE');?> (when live pricing is disabled).</p>
</dd>

<dt>Live price check:</dt>
<dd><p id="livepriceavailable">Waiting for ICAO change...</p></a>
</dd>

<dt>Hub</dt>
<?php
	if($airport->hub == '1')
		$checked = 'checked ';
	else
		$checked = '';
?>
<dd><input name="hub" type="checkbox" value="true" <?php echo $checked?>/></dd>

<dt></dt>
<dd><input type="hidden" name="action" value="<?php echo $action?>" />
	<input type="submit" name="submit" value="<?php echo $title?>" />
</dd>
</dl>
</form>

<script type="text/javascript">
$("#airporticao").bind("blur", function()
{
	$.get(<?php echo adminaction('/operations/getfuelprice')?>?icao="+$(this).val(), function(data) {
		$("#livepriceavailable").html(data);
	});
});
</script>