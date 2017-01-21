<form action="<?php echo url('/login/forgotpassword');?>" method="post">
<p><strong>Ingrese su dirección de email para obtener una nueva contraseña: </strong>
	<input type="text" name="email" />
</p>
<p><input type="hidden" name="action" value="resetpass" />
   <input type="submit" name="submit" value="Solicitar nueva contraseña" />
</p>
</form>