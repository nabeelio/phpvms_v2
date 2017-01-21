<li><a href="<?php echo url('/'); ?>">Inicio</a></li>
<?php
if(!Auth::LoggedIn())
{
	// Show these if they haven't logged in yet
?>
	<li><a href="<?php echo url('/login'); ?>">Ingresar</a></li>
	<li><a href="<?php echo url('/registration'); ?>">Registrarse</a></li>
<?php
}
else
{
	// Show these items only if they are logged in
?>
	<li><a href="<?php echo url('/profile'); ?>">Sala de Pilotos</a></li>
	
<?php
}
?>
<li><a href="<?php echo url('/pilots'); ?>">Pilotos</a></li>
<li><a href="<?php echo url('/acars') ?>">Vuelos Actuales</a></li>
<?php echo $MODULE_NAV_INC;?>
<?php
if(Auth::LoggedIn())
{
	if(PilotGroups::group_has_perm(Auth::$usergroups, ACCESS_ADMIN))
	{
		echo '<li><a href="'.fileurl('/admin').'">Centro de Administración</a></li>';
	}
?>


<li><a href="<?php echo url('/logout'); ?>">Salir</a></li>
<?php
}
?>