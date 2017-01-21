<h3>Briefing de Vuelo</h3>
<script src="http://skyvector.com/linkchart.js"></script>
<table width="98%" align="center" cellpadding="4">
	<!-- Flight ID -->
	<tr style="background-color: #333; color: #FFF;">
		<td colspan="2">Vuelo</td>
	</tr>
	<tr>
		<td colspan="2">
		<?php echo $schedule->code.$schedule->flightnum; ?>
		</td>
	</tr>
	
	<tr  style="background-color: #333; color: #FFF;">
		<td>Salida</td>
		<td>Llegada</td>
	</tr>
	
	<tr>
		<td width="50%" ><?php echo "{$schedule->depname} ($schedule->depicao)"; ?><br />
			<a href="https://pilotweb.nas.faa.gov/geo/icaoRadius.html?icao_id=<?php echo $schedule->depicao?>&radius=2"
				target="_blank">Haga click para ver NOTAMs</a>
		</td>
		<td width="50%" ><?php echo "{$schedule->arrname} ($schedule->arricao)"; ?><br />
			<a href="https://pilotweb.nas.faa.gov/geo/icaoRadius.html?icao_id=<?php echo $schedule->arricao?>&radius=2"
				target="_blank">Haga click para ver NOTAMs</a></td>
	</tr>
	
	<!-- Times Row -->
	<tr style="background-color: #333; color: #FFF;">
		<td>Hora de Salida</td>
		<td>Hora de Llegada</td>
	</tr>
	<tr>
		<td width="50%" ><?php echo "{$schedule->deptime}"; ?></td>
		<td width="50%" ><?php echo "{$schedule->arrtime}"; ?></td>
	</tr>
	
	<!-- Aircraft and Distance Row -->
	<tr style="background-color: #333; color: #FFF;">
		<td>Equipo</td>
		<td>Distancia</td>
	</tr>
	<tr>
		<td width="50%" ><?php echo "{$schedule->aircraft}"; ?></td>
		<td width="50%" ><?php echo "{$schedule->distance}"; ?></td>
	</tr>
	
	<tr style="background-color: #333; color: #FFF;">
		<td>METAR de Salida</td>
		<td>METAR de Llegada</td>
	</tr>
	<tr>
		<td width="50%" ><div id="<?php echo $schedule->depicao; ?>" class="metar">Getting Airport METAR</div> <br />
			<a href="http://flightaware.com/resources/airport/<?php echo $schedule->depicao?>/weather" target="_blank">
			Ver detalles del clima</a>
		</td>
		<td width="50%" ><div id="<?php echo $schedule->arricao; ?>" class="metar">Getting Airport METAR</div><br />
			<a href="http://flightaware.com/resources/airport/<?php echo $schedule->arricao?>/weather" target="_blank">
			Ver detalles del clima</a></td>
	</tr>
	
	<!-- Route -->
	<tr style="background-color: #333; color: #FFF;">
		<td colspan="2">Ruta</td>
	</tr>
	<tr>
		<td colspan="2">
		<?php 
		# If it's empty, insert some blank lines
		if($schedule->route == '')
		{
			echo '<br /> <br /> <br />';
		}
		else
		{
			echo strtoupper($schedule->route); 
		}
		?>
		</td>
	</tr>
	
	<!-- Notes -->
	<tr style="background-color: #333; color: #FFF;">
		<td colspan="2">Notas</td>
	</tr>
	<tr>
		<td colspan="2" style="padding: 6px;">
		<?php 
			# If it's empty, insert some blank lines
			if($schedule->notes == '')
			{
				echo '<br /> <br /> <br />';
			}
			else
			{
				echo "{$schedule->notes}"; 
			}
		?>
		</td>
	</tr>
	
	
</table>

<table width="98%" align="center" padding="2">
<tr style="background-color: #333; color: #FFF; padding: 5px;">
	<td>Datos adicionales</td>
</tr>
<tr>
	<td><a href="http://flightaware.com/analysis/route.rvt?origin=<?php echo $schedule->depicao?>&destination=<?php echo $schedule->arricao?>">Ver datos de rutas IFR recientes</a></td>
</tr>
</table>

<h3>Información y Procedimientos</h3>
<table width="98%" align="center">

	<tr style="background-color: #333; color: #FFF;">
		<td>Cartas para <?php echo $schedule->depicao?></td>
		<td>Cartas para <?php echo $schedule->arricao?></td>
	</tr>
	<tr align="center">
		<td width="50%" valign="top">
			<a href="http://www.airnav.com/airport/<?php echo $schedule->depicao?>#ifr" target="_blank">
			<img border="0" src="http://flightaware.com/resources/airport/<?php echo $schedule->depicao?>/APD/AIRPORT+DIAGRAM/png"
				width="387px" height="594px" alt="No hay cartas disponibles" /></a>
		</td>
		<td width="50%" valign="top">
			<a href="http://www.airnav.com/airport/<?php echo $schedule->arricao?>#ifr" target="_blank">
			<img border="0" src="http://flightaware.com/resources/airport/<?php echo $schedule->arricao?>/APD/AIRPORT+DIAGRAM/png" 
				width="387px" height="594px" alt="No hay cartas disponibles" /></a>
		</td>
	
	</tr>
</table>

<h3>Weather</h3>
<div align="center">
	<p><img src="http://flightaware.com/resources/wx/us_depiction.gif" /></p>
	<p><img src="http://flightaware.com/resources/wx/surface_analysis.gif" /></p>
	<p><img src="http://flightaware.com/resources/wx/national_radar.gif" /></p>
	<p><img src="http://flightaware.com/resources/wx/us_high_level_weather_12z.gif" /></p>
	<p><img src="http://flightaware.com/resources/wx/severe_outlook_day_1.gif" /></p>
	<a href="http://flightaware.com/resources/weather_maps/" target="_blank">Ver gráficos adicionales del clima</a>
</div>
<div align="right" style="font-size: small;">Datos cortesía de <a href="http://flightaware.com" target="_new">FlightAware</a></div>
<br />