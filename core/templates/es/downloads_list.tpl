<h1>Descargas</h1>

<?php 
if(!$allcategories)
{
	echo '¡No hay descargas disponibles!';
	return;
}

foreach($allcategories as $category)
{
?>
<p><h2><strong><?php echo $category->name?></strong></h2></p>
<ul>

<?php	
	# This loops through every download available in the category
	$alldownloads = DownloadData::GetDownloads($category->id);
	
	if(!$alldownloads)
	{
		echo 'No hay descargas en esta categoría';
		$alldownloads = array();
	}
	
	foreach($alldownloads as $download)
	{
?>
	<li>
		<a href="<?php echo url('/downloads/dl/'.$download->id);?>">
			<?php echo $download->name?></a><br />
	      <?php echo $download->description?><br />
          <em>Descargado <?php echo $download->hits?> veces</em></li>
<?php
	}
?><br />
</ul>
	<?php
}
?>