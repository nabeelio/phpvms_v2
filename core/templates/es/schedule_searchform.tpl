<h3>Buscas Itinerarios</h3>
<form id="form" action="<?php echo actionurl('/schedules/view');?>" method="post">

<div id="tabcontainer">
	<ul>
		<li><a href="#depapttab"><span>Por Aeropuerto de Salida</span></a></li>
		<li><a href="#arrapttab"><span>Por Aeropuerto de Llegada</span></a></li>
		<li><a href="#aircrafttab"><span>Por Tipo de Aeronave</span></a></li>
		<li><a href="#distance"><span>Por Distancia</span></a></li>
	</ul>
	<div id="depapttab">
		<p>Seleccione su aeropuerto de salida:</p>
		<select id="depicao" name="depicao">
		<option value="">Seleccionar Todos</option>
		<?php
		if(!$depairports) $depairports = array();
			foreach($depairports as $airport)
			{
				echo '<option value="'.$airport->icao.'">'.$airport->icao
						.' ('.$airport->name.')</option>';
			}
		?>
			
		</select>
		<input type="submit" name="submit" value="Buscar Vuelos" />
	</div>
	<div id="arrapttab">
		<p>Seleccione su aeropueto de llegada:</p>
		<select id="arricao" name="arricao">
			<option value="">Seleccionar Todos</option>
		<?php
		if(!$depairports) $depairports = array();
			foreach($depairports as $airport)
			{
				echo '<option value="'.$airport->icao.'">'.$airport->icao
						.' ('.$airport->name.')</option>';
			}
		?>
			
		</select>
		<input type="submit" name="submit" value="Buscar Vuelos" />
	</div>
	<div id="aircrafttab">
		<p>Seleccione Aeronave:</p>
		<select id="equipment" name="equipment">
			<option value="">Seleccione Equipo</option>
		<?php
		
		if(!$equipment) $equipment = array();
		foreach($equipment as $equip)
		{
			echo '<option value="'.$equip->name.'">'.$equip->name.'</option>';
		}
		
		?>
		</select>
		<input type="submit" name="submit" value="Buscar Vuelos" />
	</div>
	<div id="distance">
		<p>Seleccione Distancia:</p>
		<select id="type" name="type">
			<option value="greater">Mayor que</option>
			<option value="less">Menor que</option>
		</select>
		<input type="text" name="distance" value="" />
		<input type="submit" name="submit" value="Buscar Vuelos" />
	</div>
</div>

<p>
<input type="hidden" name="action" value="findflight" />
</p>
</form>
<script type="text/javascript">

</script>
<hr>