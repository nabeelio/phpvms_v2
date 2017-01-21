Estimado(a) <?php echo $pilot->firstname.' '.$pilot->lastname?>,

Su usuario ha sido marcado como retirado debido a que ha estado inactivo por más de <?php echo Config::Get('PILOT_INACTIVE_TIME')?> días. Para dejar de estar en retiro, debe enviar un PIREP.


Gracias,
El equipo de <?php echo SITE_NAME; ?>