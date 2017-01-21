	</div>
	</div>
	<div style="clear:both"></div>
	<div id="footer">
	<p>
		"<?php echo randquote(); ?>"
		<br /> 
		Copyright &copy; 2007 - <?php echo date('Y')?> 
		<a href="http://www.phpvms.net/" target="_new">phpVMS</a>, 
		<a href="http://www.nsslive.net" target="_new">nsslive.net</a>
		<br />
		<a href="<?php echo SITE_URL?>/admin/index.php/dashboard/about">License & About</a> | 
		Version <?php echo PHPVMS_VERSION; ?>
	</p>
  </div>
</div>
<?php
Template::Show('core_footer.tpl');
?>

</body>
</html>
