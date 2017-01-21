<h3>Detalles de Itinerario</h3>
<div class="indent">
<strong>Número de Vuelo: </strong> <?php echo $schedule->code.$schedule->flightnum ?><br />
<strong>Salida: </strong><?php echo $schedule->depname ?> (<?php echo $schedule->depicao ?>) a las <?php echo $schedule->deptime ?><br />
<strong>Llegada: </strong><?php echo $schedule->arrname ?> (<?php echo $schedule->arricao ?>) a las <?php echo $schedule->arrtime ?><br />
<?php
if($schedule->route!='')
{ ?>
<strong>Ruta: </strong><?php echo $schedule->route ?><br />
<?php
}?>
<br />
<strong>Información Climatológica</strong>
<div id="<?php echo $schedule->depicao ?>" class="metar">Obteniento información de METAR para <?php echo $schedule->depicao ?></div>
<div id="<?php echo $schedule->arricao ?>" class="metar">Obteniendo información de METAR para <?php echo $schedule->arricao ?></div>
<br />
<strong>Frecuencia del itinerario</strong>
<div align="center">
<?php
/*
	Added in 2.0!
*/
$chart_width = '800';
$chart_height = '170';

/* Don't need to change anything below this here */
?>
<div align="center" style="width: 100%;">
	<div align="center" id="pireps_chart"></div>
</div>

<script type="text/javascript" src="<?php echo fileurl('/lib/js/ofc/js/swfobject.js')?>"></script>
<script type="text/javascript">
swfobject.embedSWF("<?php echo fileurl('/lib/js/ofc/open-flash-chart.swf');?>", 
	"pireps_chart", "<?php echo $chart_width;?>", "<?php echo $chart_height;?>", 
	"9.0.0", "expressInstall.swf", 
	{"data-file":"<?php echo actionurl('/schedules/statsdaysdata/'.$schedule->id);?>"});
</script>
<?php
/* End added in 2.0
*/
?>
</div>