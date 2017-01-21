<h3>Cambiar Contraseña</h3>
<form action="<?php echo url('/profile');?>" method="post">
<dl>

	<dt>Ingrese su nueva contraseña</dt>
	<dd><input type="password" id="password" name="password1" value="" /></dd>
	
	<dt>Repita su nueva contraseña</dt>
	<dd><input type="password" name="password2" value="" /></dd>
	
	<dt>Ingrese su antigua contraseña</dt>
	<dd><input type="password" name="oldpassword" /></dd>
	
	<dt></dt>
	<dd><input type="hidden" name="action" value="changepassword" />
		<input type="submit" name="submit" value="Guardar contraseña" />
	</dd>
</dl>
</form>