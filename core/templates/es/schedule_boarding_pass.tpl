<?php
# This will only show this message if it's not a popup window
# Ends on line 13-15
if(!isset($_GET['newwindow']))
{
?>
<h3>Requerimientos para check-in en línea</h3>

<p>Para continuar a través del Control de Seguridad, necesita tener un documento gubernamental con fotografía y ademas su Pase de Abordaje o Documento de Seguridad. Clientes menores de 18 años no requiren mostrar documento gubernamental.</p>

<p><a href="#" 
	onclick="window.open('<?php echo actionurl('/schedules/boardingpass/'.$schedule->id.'?newwindow');?>'); return false;">Abrir en nueva ventana para imprimir</a></p>
<?php
}
?>

<style>
/* Some integrated styles here, for the popup */
.boardingpass {
	font-family: Tahoma, Verdana;
	font-size: 14px;
}
.boardingpass h3 {
	background: none;
	padding-left: 3px;
	padding-bottom: 2px;
}
.boardingpass .thickline
{
	background: #333;
	height: 2px;
}
</style>
<table width="90%" class="boardingpass">
	<tr>
		<td width="1%"><img src="<?php echo SITE_URL?>/lib/images/barcode.png" /></td>
		<td align="left"><h3><?php echo SITE_NAME;?></h3></td>
	</tr>
	<tr>
		<td colspan="2"><h3>Pase de Abordaje</h3></td>
	</tr>
	<tr class="thickline">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td valign="top">
			<table class="boardingpass">
				<tr>
				<td>
					<strong>Fecha:</strong> <br />
					<strong>Nombre: </strong> <br />
					<strong>Número de Pasajero Frecuente: </strong> <br />
					<strong>Número de Pase de Abordaje:</strong> 
				</td>
				<td>
					<?php echo date('Y-m-d'); ?><br />
					<?php echo Auth::$userinfo->firstname.' '.Auth::$userinfo->lastname?><br />
					<?php echo Auth::$userinfo->code.strtoupper(substr(md5(Auth::$userinfo->pilotid), 0, 6))?><br />
					<?php echo $schedule->bidid; ?><br />
				</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<strong>Puerta:</strong> <?php # We are gonna get a random gate
				echo chr(rand(65, 90)); // echo's a random letter between A and Z
				echo rand(1, 30);		// gate # (between 1 and 30)
			?><br />
			<strong>Confirmación:</strong>
			<?php 
				# Generate a hash from the bid id, and get the first 6 characters
				# That'll be used for our confirmation number, and upper-case them
				echo strtoupper(substr(md5($schedule->bidid), 0, 6));
			?>
		</td>
	</tr>
	<tr class="thickline">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td valign="top">
			<strong>Vuelo: </strong><?php echo $schedule->code.$schedule->flightnum?><br />
			<strong>Sale: </strong><?php echo $schedule->deptime; ?><br />
			<strong>Llega: </strong><?php echo $schedule->arrtime;?><br />
		</td>
		<td valign="top">
			<strong>Equipo: </strong><?php echo $schedule->aircraft?> <br />	
			<?php echo "$schedule->depname ($schedule->depicao)";?><br />
			<?php echo "$schedule->arrname ($schedule->arricao)"; ?><br />
		</td>
	</tr>
</table>