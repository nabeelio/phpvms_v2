<h3>Contáctenos</h3>
<form method="post" action="<?php echo url('/contact'); ?>">
  <table width='100%' border='0'>
    <tr>
      <td><strong>Nombre:</strong></td>
      <td>
		<?php
		if(Auth::LoggedIn())
		{
			echo Auth::$userinfo->firstname .' '.Auth::$userinfo->lastname;
			echo '<input type="hidden" name="name" 
					value="'.Auth::$userinfo->firstname 
							.' '.Auth::$userinfo->lastname.'" />';
		}
		else
		{
		?>
			<input type="text" name="name" value="" />
			<?php
		}
		?>
      </td>
    </tr>
    <tr>
		<td width="1%" nowrap><strong>E-Mail:</strong></td>
		<td>
		<?php
		if(Auth::LoggedIn())
		{
			echo Auth::$userinfo->email;
			echo '<input type="hidden" name="name" 
					value="'.Auth::$userinfo->email.'" />';
		}
		else
		{
		?>
			<input type="text" name="email" value="" />
			<?php
		}
		?>
		</td>
	</tr>

	<tr>
		<td><strong>Asunto: </strong></td>
		<td><input type="text" name="subject" value="<?php echo $_POST['subject'];?>" /></td>
	
	</tr>
    <tr>
      <td><strong>Mensaje:</strong></td>
      <td>
		<textarea name="message" cols='45' rows='5'><?php echo $_POST['message'];?></textarea>
      </td>
    </tr>
    
    <tr>
		<td width="1%" nowrap><strong>Captcha</strong></td>
		<td>
		<?php
		echo recaptcha_get_html(Config::Get('RECAPTCHA_PUBLIC_KEY'), $captcha_error);
		?>
		</td>
	</tr>
	
    <tr>
		<td>
			<input type="hidden" name="loggedin" value="<?php echo (Auth::LoggedIn())?'true':'false'?>" />
		</td>
		<td>
          <input type="submit" name="submit" value='Enviar Mensaje'>
		</td>
    </tr>
  </table>
</form>