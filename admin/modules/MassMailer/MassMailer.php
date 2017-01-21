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
 * 
 *This particular section was built by Cale Cunningham; basically a modified version
 *of the contact form found in the core.
 */

class MassMailer extends CodonModule 
{
	
	public function HTMLHead()
	{
		$this->set('sidebar', 'sidebar_mailer.tpl');
	}
	
	public function index()
	{
		$this->set('allgroups', PilotGroups::getAllGroups());
		$this->render('mailer_form.tpl');
	}
	
	public function sendmail()
	{
		echo '<h3>Sending email</h3>';
		if($this->post->subject == '' || trim($this->post->message) == '')
		{
			$this->set('message', 'You must enter a subject and message!');
			$this->render('core_error.tpl');
			return;
		}
		
		if(count($this->post->groups) == 0)
		{
			$this->set('message', 'You must select groups to send to!');
			$this->render('core_error.tpl');
			return;
		}
		
		echo 'Sending email...<br />';
		
		$pilotarray = array();
		//Begin the nice long assembly of e-mail addresses
		foreach($this->post->groups as $groupid)
		{
			if($groupid == 'all')
			{
				$all_pilots = PilotData::findPilots(array());
				foreach($all_pilots as $pilot)
				{
					$pilotarray[$pilot->pilotid] = $pilot;
				}
				
				break;
			}
			else
			{
				$tmp = PilotGroups::getUsersInGroup($groupid);
				if(count($tmp) == 0 || ! is_array($tmp))
				{
					continue;
				}
				
				foreach($tmp as $pilot)
				{
					$pilotarray[$pilot->pilotid] = $pilot;
				}
			}
		}
		
		$subject = DB::escape($this->post->subject);
		$message = stripslashes($this->post->message). PHP_EOL . PHP_EOL;
		$fromname = Auth::$userinfo->firstname . ' ' . Auth::$userinfo->lastname;
		$fromemail = Auth::$userinfo->email;
		
		# Do some quick fixing of obvious formatting errors
		$message = str_replace('<br>', '<br />', $message);
		foreach($pilotarray as $pilot)
		{
			echo 'Sending for '.$pilot->firstname.' '.$pilot->lastname.'<br />';
			
			# Variable replacements
			$send_message = str_replace('{PILOT_FNAME}', $pilot->firstname, $message);
			$send_message = str_replace('{PILOT_LNAME}', $pilot->lastname, $send_message);
			$send_message = str_replace('{PILOT_ID}', PilotData::GetPilotCode($pilot->code, $pilot->pilotid), $send_message);
			$send_message = utf8_encode($send_message);
			
			Util::SendEmail($pilot->email, $subject, $send_message, $fromname, $fromemail);
		}
		
		echo 'Complete!';
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Sent pass mail');
		return;
	}
	
}
