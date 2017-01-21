<h1>Ingresar</h1>
<form name="loginform" action="<?php echo url('/login');?>" method="post">
<?php echo "<?xml version='1.0'?>"; ?>
<?php
if(isset($message))
	echo '<p class="error">'.$message.'</p>';
?>
<dl>
	<dt>Dirección de E-mail:</dt>
	<dd><input type="text" name="email" value="" />
	
	<dt>Contraseña:</dt>
	<dd><input type="password" name="password" value="" />
	   
	<dt></dt>
	<dd>¿Mantener sesión? <input type="checkbox" name="remember" /></dd>

	<dt></dt>
	<dd><input type="hidden" name="redir" value="index.php/profile" />
		<input type="hidden" name="action" value="login" />
		<input type="submit" name="submit" value="Ingresar" />

	
		
	<dt></dt>
	<dd><a href="<?php echo url('Login/forgotpassword'); ?>">Olvidé mi contraseña</a></dd>
</dl>
</form>