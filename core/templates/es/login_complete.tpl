<?php
	if($redir == '')
		$redir = SITE_URL;
		
?><div align="center">
<p>Usted será redireccionado en unos segundos, o haga click debajo para ser redireccionado.</p>
<p><a href="<?php echo $redir;?>">Haga click aquí para ser redireccionado</a></p>
</div>

<script type="text/javascript"> 
    window.location = "<?php echo $redir;?>";
</script>