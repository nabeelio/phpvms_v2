<h3>Enviar un Reporte de Vuelo</h3>
<?php
if(isset($message))
	echo '<div id="error">'.$message.'</div>';
?>
<form action="<?php echo url('/pireps/mine');?>" method="post">
<dl>
	<dt>Piloto:</dt>
	<dd><strong><?php echo Auth::$userinfo->firstname . ' ' . Auth::$userinfo->lastname;?></strong></dd>
	
	<dt>Seleccione Aerolínea:</dt>
	<dd>
		<select name="code" id="code">
			<option value="">Seleccione su aerolínea</option>
		<?php
		foreach($allairlines as $airline)
		{
			$sel = ($_POST['code'] == $airline->code || $bid->code == $airline->code)?'selected':'';
				
			echo '<option value="'.$airline->code.'" '.$sel.'>'.$airline->code.' - '.$airline->name.'</option>';
		}
		?>
		</select>
	</dd>
	
	<dt>Ingrese Número de Vuelo:</dt>
	<dd><input type="text" name="flightnum" value="<?php if(isset($bid->flightnum)) { echo $bid->flightnum; }?><?php if(isset($_POST['flightnum'])) { echo $_POST['flightnum'];} ?>" /></dd>
	
	<dt>Seleccione Aeropuerto de Orígen:</dt>
	<dd>
		<div id="depairport">
		<select id="depicao" name="depicao">
			<option value="">Seleccione un aeropuerto de orígen</option>
			<?php
			foreach($allairports as $airport)
			{
				$sel = ($_POST['depicao'] == $airport->icao || $bid->depicao == $airport->icao)?'selected':'';
				
				echo '<option value="'.$airport->icao.'" '.$sel.'>'.$airport->icao . ' - '.$airport->name .'</option>';
			}
			?>
		</select>
		</div>
	</dd>
	
	<dt>Seleccione Aeropuerto de Destino:</dt>
	<dd>
		<div id="arrairport">
		<select id="arricao" name="arricao">
			<option value="">Seleccione un aeropuerto de destino</option>
			<?php
			foreach($allairports as $airport)
			{
				$sel = ($_POST['arricao'] == $airport->icao || $bid->arricao == $airport->icao)?'selected':'';
				
				echo '<option value="'.$airport->icao.'" '.$sel.'>'.$airport->icao . ' - '.$airport->name .'</option>';
			}
			?>
		</select>
		</div>
	</dd>
	
	<dt>Seleccione Aeronave:</dt>
	<dd>
		<select name="aircraft" id="aircraft">
			<option value="">Seleccione la Aeronave para este vuelo</option>
		<?php
		
		foreach($allaircraft as $aircraft)
		{
			
			/*	Skip any aircraft which have aircraft that the pilot
				is not rated to fly (according to RANK) 
			*/
			if(Config::Get('RESTRICT_AIRCRAFT_RANKS') === true)
			{
				/*	This means the aircraft rank level is higher than
					what the pilot's ranklevel, so just do "continue"
					and move onto the next route in the list 
				 */
				if($aircraft->ranklevel > Auth::$userinfo->ranklevel)
				{
					continue;
				}
			}
			
			$sel = ($_POST['aircraft'] == $aircraft->name || $bid->registration == $aircraft->registration)?'selected':'';
			
			echo '<option value="'.$aircraft->id.'" '.$sel.'>'.$aircraft->name.' - '.$aircraft->registration.'</option>';
		}
		?>
		</select>
	</dd>

	<?php
	// List all of the custom PIREP fields
	if(!$pirepfields) $pirepfields = array();
	foreach($pirepfields as $field)
	{
	?>
		<dt><?php echo $field->title ?></dt>
		<dd>
		<?php
		
		// Determine field by the type
		
		if($field->type == '' || $field->type == 'text')
		{
		?>
			<input type="text" name="<?php echo $field->name ?>" value="<?php echo $_POST[$field->name] ?>" />
		<?php
		} 
		elseif($field->type == 'textarea')
		{
			echo '<textarea name="'.$field->name.'">'.$field->values.'</textarea>';
		}
		elseif($field->type == 'dropdown')
		{
			$values = explode(',', $field->options);
			
			echo '<select name="'.$field->name.'">';
			foreach($values as $value)
			{
				$value = trim($value);
				echo '<option value="'.$value.'">'.$value.'</option>';
			}
			echo '</select>';		
		}
		?>
		
		</dd>
	<?php
	}
	?>
	
	<dt>Combustible Utilizado</dt>
	<dd><input type="text" name="fuelused" value="<?php echo $_POST['fuelused']; ?>" />
		<p>Este es el combustible utilizado en este vuelo en <?php echo Config::Get('LIQUID_UNIT_NAMES', Config::Get('LiquidUnit'))?></p></dd>
	
	<dt>Tiempo de Vuelo</dt>
	<dd><input type="text" name="flighttime" value="<?php echo $_POST['flighttime'] ?>" />
		<p>Ingrese como horas - "5.30" es cinco horas y treinta minutos</p></dd>
		
	<dt>Ruta</dt>
	<dd><textarea name="route" style="width: 100%"><?php echo (!isset($_POST['route'])) ? $bid->route : $_POST['route']; ?></textarea>
		<p>Ingrese la ruta volada, o se tomará el predeterminado del itinerario</p></dd>
	
	<dt>Comentarios</dt>
	<dd><textarea name="comment" style="width: 100%"><?php echo $_POST['comment'] ?></textarea></dd>
	
	<dt></dt>
	<dd><?php $bidid = ( isset($bid) )? $bid->bidid:$_POST['bid']; ?>
		<input type="hidden" name="bid" value="<?php echo $bidid ?>" />
		<input type="submit" name="submit_pirep" value="Enviar Reporte de Vuelo" /></dd>
</dl>

</form>