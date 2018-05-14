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
 
class Registration extends CodonModule
{
	public function HTMLHead()
	{
		/*Show our password strength checker
			*/
		if($this->get->page == 'register')
		{
			$this->renderTemplate('registration_javascript.tpl');
		}
	}
		
		
	public function index()
	{
		if(Auth::LoggedIn()) // Make sure they don't over-ride it
		{
			$this->render('login_already.tpl');
			return;
		}
			
		
		if(isset($_POST['submit']))
		{
			$this->ProcessRegistration();
		}
		else
		{
			$this->ShowForm();
		}
	}
		
	protected function ShowForm()
	{
		
		$this->set('extrafields', RegistrationData::GetCustomFields());
		$this->set('allairlines', OperationsData::GetAllAirlines(true));
		$this->set('allhubs', OperationsData::GetAllHubs());
		$this->set('countries', Countries::getAllCountries());
		
		$this->render('registration_mainform.tpl');
	}
	
	protected function ProcessRegistration()
	{
			
		// Yes, there was an error
		if(!$this->VerifyData())
		{
			$this->ShowForm();
		}
		else
		{
			$data = array(
				'firstname' => $this->post->firstname,
				'lastname' => $this->post->lastname,
				'email' => $this->post->email,
				'password' => $this->post->password1,
				'code' => $this->post->code,
				'location' => $this->post->location,
				'hub' => $this->post->hub,
				'confirm' => false
			);
				
			if(CodonEvent::Dispatch('registration_precomplete', 'Registration', $_POST) == false)
			{
				return false;
			}
			
			$ret = RegistrationData::CheckUserEmail($data['email']);
			
			if($ret)
			{
				$this->set('error', Lang::gs('email.inuse'));
				$this->render('registration_error.tpl');
				return false;
			}
			
			$val = RegistrationData::AddUser($data);
			if($val == false)
			{
				$this->set('error', RegistrationData::$error);
				$this->render('registration_error.tpl');
				return;
			}
			else
			{
				$pilotid = RegistrationData::$pilotid;
				
				/* Automatically confirm them if that option is set */
				if(Config::Get('PILOT_AUTO_CONFIRM') == true)
				{
					PilotData::AcceptPilot($pilotid);
					RanksData::CalculatePilotRanks();
					
					$pilot = PilotData::GetPilotData($pilotid);
					$this->set('pilot', $pilot);
					$this->render('registration_autoconfirm.tpl');
				}
				/* Otherwise, wait until an admin confirms the registration */
				else
				{
					RegistrationData::SendEmailConfirm($email, $firstname, $lastname);
					$this->render('registration_sentconfirmation.tpl');
				}
			}
			
			CodonEvent::Dispatch('registration_complete', 'Registration', $_POST);
			
			// Registration email/show user is waiting for confirmation
			$sub = 'A user has registered';
			$message = "The user {$data['firstname']} {$data['lastname']} ({$data['email']}) has registered, and is awaiting confirmation.";
			
			$email = Config::Get('EMAIL_NEW_REGISTRATION');
			if(empty($email))
			{
				$email = ADMIN_EMAIL;
			}
			
			Util::SendEmail($email, $sub, $message);
			
			// Send email to user
			$this->set('firstname', $data['firstname']);
			$this->set('lastname', $data['lastname']);
			$this->set('userinfo', $data);
			
			$message = Template::Get('email_registered.tpl', true);
			Util::SendEmail($data['email'], 'Registration at '.SITE_NAME, $message);
			
			$rss = new RSSFeed('Latest Pilot Registrations', SITE_URL, 'The latest pilot registrations');
			$allpilots = PilotData::GetLatestPilots();
			
			foreach($allpilots as $pilot)
			{
				$rss->AddItem('Pilot '.PilotData::GetPilotCode($pilot->code, $pilot->pilotid)
								. ' ('.$pilot->firstname .' ' . $pilot->lastname.')',
								SITE_URL.'/admin/index.php?admin=pendingpilots','','');
			}
		
			$rss->BuildFeed(LIB_PATH.'/rss/latestpilots.rss');
		}
	}

	/*
	 * Process all the registration data
	 */
	protected function VerifyData()
	{
		$error = false;
		$recaptcha = new \ReCaptcha\ReCaptcha(Config::Get('RECAPTCHA_PRIVATE_KEY'));
		$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		
		if(!$resp->isSuccess())
		{
			$error = true;
			$this->set('captcha_error', $resp->error);
		}
		else
			$this->set('captcha_error', '');
		
		/* Check the firstname and last name
		 */
		if($this->post->firstname == '')
		{
			$error = true;
			$this->set('firstname_error', true);
		}
		else
			$this->set('firstname_error', '');
		
		/* Check the last name
		 */
		if($this->post->lastname == '')
		{
			$error = true;
			$this->set('lastname_error', true);
		}
		else
			$this->set('lastname_error', '');
		
		/* Check the email address
		 */
		if(filter_var($this->post->email, FILTER_VALIDATE_EMAIL) == false)
		{
			$error = true;
			$this->set('email_error', true);
		}
		else
			$this->set('email_error', '');
		
		/* Check the location
		 */
		if($this->post->location == '')
		{
			$error = true;
			$this->set('location_error', true);
		}
		else
			$this->set('location_error', '');
		
		// Check password length
		if(strlen($this->post->password1) <= 5)
		{
			$error = true;
			$this->set('password_error', 'The password is too short!');
		}
		else
			$this->set('password_error', '');
		
		// Check is passwords are the same
		if($this->post->password1 != $this->post->password2)
		{
			$error = true;
			$this->set('password_error', 'The passwords do not match!');
		}
		else
			$this->set('password_error', '');
		
		/* Check if they agreed to the statement

		if(!$_POST['agree'])
		{
			$error = true;
			$this->set('agree_error', true);
		}
		else
			$this->set('agree_error', '');
		 */
		if($error == true)
		{
			return false;
		}
		
		return true;
	}
}
