<h3>Registro</h3>
<p>Bienvenido al formulario de registro de <?php echo SITE_NAME; ?>. Despues de registrarse, un miembro del staff le notificará sobre su membresía.</p>
<form method="post" action="<?php echo url('/registration');?>">
<dl>
	<dt>Nombre: *</dt>
	<dd><input type="text" name="firstname" value="<?php echo Vars::POST('firstname');?>" />
		<?php
			if($firstname_error == true)
				echo '<p class="error">Por favor ingrese su nombre</p>';
		?>
	</dd>
	
	<dt>Apellidos: *</dt>
	<dd><input type="text" name="lastname" value="<?php echo Vars::POST('lastname');?>" />
		<?php
			if($lastname_error == true)
				echo '<p class="error">Por favor ingrese sus apellidos</p>';
		?>
	</dd>
	
	<dt>Dirección de Email: *</dt>
	<dd><input type="text" name="email" value="<?php echo Vars::POST('email');?>" />
		<?php
			if($email_error == true)
				echo '<p class="error">Por favor ingrese su email</p>';
		?>
	</dd>
	
	<dt>Seleccione su Aerolínea: *</dt>
	<dd>
		<select name="code" id="code">
		<?php
		foreach($allairlines as $airline)
		{
			echo '<option value="'.$airline->code.'">'.$airline->code.' - '.$airline->name.'</option>';
		}
		?>
		</select>
	</dd>
	
	<dt>Hub: *</dt>
	<dd>
		<select name="hub" id="hub">
		<?php
		foreach($allhubs as $hub)
		{
			echo '<option value="'.$hub->icao.'">'.$hub->icao.' - ' . $hub->name .'</option>';
		}
		?>
		</select>
	</dd>

	<dt>Ubicación: *</dt>
	<dd><select name="location">
		<?php
			foreach($countries as $countryCode=>$countryName)
			{
				if(Vars::POST('location') == $countryCode)
					$sel = 'selected="selected"';
				else	
					$sel = '';
					
				echo '<option value="'.$countryCode.'" '.$sel.'>'.$countryName.'</option>';
			}
		?>
		</select>
		<?php
			if($location_error == true)
				echo '<p class="error">Please enter your location</p>';
		?>
	</dd>
	
	<dt>Contraseña: *</dt>
	<dd><input id="password" type="password" name="password1" value="" /></dd>
	
	<dt>Ingrese nuevamente su contraseña: *</dt>
	<dd><input type="password" name="password2" value="" />
		<?php
			if($password_error != '')
				echo '<p class="error">'.$password_error.'</p>';
		?>
	</dd>
		
	<?php
	
	//Put this in a seperate template. Shows the Custom Fields for registration
	Template::Show('registration_customfields.tpl');
	
	?>
	
	<dt>reCaptcha</dt>
	<dd>
		<?php
			echo recaptcha_get_html(Config::Get('RECAPTCHA_PUBLIC_KEY'), $captcha_error);
		?>
	</dd>
		
	<dt></dt>
	<dd><p>Al hacer click en Registrar, esta aceptando los <a href="<?php echo url('/pages/reglamento'); ?>" target="_new">términos y condiciones</a></p></dd>
	<dt></dt>
	<dd><input type="submit" name="submit" value="¡Registrar!" /></dd>
</dl>
</form>
