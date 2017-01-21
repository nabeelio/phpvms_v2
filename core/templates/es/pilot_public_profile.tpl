<?php
if(!$userinfo)
{
	echo '<h3>¡Este piloto no existe!</h3>';
	return;
}
?>
<h3>Perfil de <?php echo $userinfo->firstname . ' ' . $userinfo->lastname?></h3>
<table>
	<tr>
		<td align="center" valign="top">
			<?php
			if(!file_exists(SITE_ROOT.AVATAR_PATH.'/'.$pilotcode.'.png'))
			{
				echo 'Sin avatar';
			}
			else
			{
				echo '<img src="'.SITE_URL.AVATAR_PATH.'/'.$pilotcode.'.png'.'" alt="Sin Avatar" /> ';
			}
			?>
			<br /><br />
			<img src="<?php echo $userinfo->rankimage?>"  alt="" />
		</td>
		<td valign="top">
			<ul>
				<li><strong>ID del Piloto: </strong><?php echo $pilotcode ?></li>
				<li><strong>Rango: </strong><?php echo $userinfo->rank;?></li>
				<li><strong>Total de Vuelos: </strong><?php echo $userinfo->totalflights?></li>
				<li><strong>Total de Horas: </strong><?php echo Util::AddTime($userinfo->totalhours, $userinfo->transferhours); ?></li>
				<li><strong>Ubicación: </strong>
					<img src="<?php echo Countries::getCountryImage($userinfo->location);?>" 
								alt="<?php echo Countries::getCountryName($userinfo->location);?>" />
					<?php echo Countries::getCountryName($userinfo->location);?> 
				</li>
			
				<?php
				// Show the public fields
				if($allfields)
				{
					foreach($allfields as $field)
					{
						echo "<li><strong>$field->title: </strong>$field->value</li>";
					}
				}
				?>
			</ul>
			
			<p>
			<strong>Premios</strong>
			<?php
			if(is_array($allawards))
			{			
			?>
			<ul>
				<?php foreach($allawards as $award)
				{ 
					/* To show the image:
					
						<img src="<?php echo $award->image?>" alt="<?php echo $award->descrip?>" />
					*/
				
				?>
					<li><?php echo $award->name ?></li>
				<?php } ?>
			</ul>	
			<?php
			}
			?>
		</p>
		</td>
	
	</tr>
</table>
<?php
/*
	Added in 2.0!
*/
$chart_width = '800';
$chart_height = '250';

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
	{"data-file":"<?php echo actionurl('/pilots/statsdaysdata/'.$userinfo->pilotid);?>"});
</script>