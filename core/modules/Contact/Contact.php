<?php
/**
 * phpVMS - Virtual Airline Administration Software
 * Copyright (c) 2008 Nabeel Shahzad
 * For more information, visit www.phpvms.net
 *	Forums: http://www.phpvms.net/forum
 *	Documentation: http://www.phpvms.net/docs
 *
 * phpVMS is licenced under the following license:
 *   Creative Commons Attribution Non-commercial Share Alike (by-nc-sa)
 *   View license.txt in the root, or visit http://creativecommons.org/licenses/by-nc-sa/3.0/
 *
 * @author Nabeel Shahzad
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.phpvms.net
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

class Contact extends CodonModule 
{
	public function index()
	{
		require_once CORE_LIB_PATH.'/recaptcha/recaptchalib.php';


		if($this->post->submit)	{
			if(Auth::LoggedIn() == false) {					
				# Make sure they entered an email address
				if(trim($this->post->name) == '' || trim($this->post->email) == '') {
					$this->set('message', 'You must enter a name and email!');
					$this->render('core_error');
					return;
				}
			}
			
			$resp = recaptcha_check_answer (Config::Get('RECAPTCHA_PRIVATE_KEY'),
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
			
			// Check the captcha thingy
			if(!$resp->is_valid) {
				$this->set('captcha_error', $resp->error);
				$this->set('message', 'You failed the captcha test!');
				$this->render('contact_form');
				return;
			}
			
			if($this->post->subject == '' || trim($this->post->message) == '') {
				$this->set('message', 'You must enter a subject and message!');
				$this->render('core_error');
				return;
			}
			
			$subject = 'New message from '.$this->post->name.' - "'.$this->post->subject.'"';
			$message = DB::escape($this->post->message) . PHP_EOL . PHP_EOL;
			
			unset($_POST['recaptcha_challenge_field']);
			unset($_POST['recaptcha_response_field']);
			
			foreach($_POST as $field=>$value) {
				$message.="-$field = $value".PHP_EOL;
			}
			
			$message = nl2br($message);
			$message = utf8_encode($message);
			Util::SendEmail(ADMIN_EMAIL, $subject, $message);
			
			$this->render('contact_sent');
			return;
		}		
		
		# Just a simple addition
		$rand1 = rand(1, 10);
		$rand2 = rand(1, 10);
		
		$this->set('rand1', $rand1);
		$this->set('rand2', $rand2);		
		
		$tot = $rand1 + $rand2;
		//echo "total: $tot <br />";
		SessionManager::Set('captcha_sum', $tot);
		
		//echo 'output of $_SESSION: <br />';
		//print_r($_SESSION);
		
		$this->render('contact_form');
	}
	
}
