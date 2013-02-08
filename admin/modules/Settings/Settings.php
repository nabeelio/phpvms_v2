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
 * @package module_admin_settings
 */
 
class Settings extends CodonModule {

	public function __construct() {
		parent::__construct();
	}
	
	public function HTMLHead() {

		switch($this->controller->function) {
			case '':
			case 'settings':
				$this->set('sidebar', 'sidebar_settings.php');
				break;
		
			case 'customfields':
				$this->set('sidebar', 'sidebar_customfields.php');
				break;
				
			case 'pirepfields':
				$this->set('sidebar', 'sidebar_pirepfields.php');
				break;
		}
	}
	
	public function index() {
		$this->settings();
	}
	
	public function settings() {
        $this->checkPermission(EDIT_SETTINGS);

		if(isset($this->post->action)) {
			switch($this->post->action) {
				case 'addsetting':
					$this->AddSetting();
					break;
				case 'savesettings':
					$this->save_settings_post();
					
					break;
			}
		}
		
		$this->ShowSettings();
	}
	
	
	public function addfield() {
        $this->checkPermission(EDIT_PROFILE_FIELDS);
		$this->set('title', Lang::gs('settings.add.field'));
		$this->set('action', 'addfield');
		
		$this->render('settings_addcustomfield.php');
	}
	
	public function editfield() {
        $this->checkPermission(EDIT_PROFILE_FIELDS);
		$this->set('title', Lang::gs('settings.edit.field'));
		$this->set('action', 'savefield');
		$this->set('field', SettingsData::GetField($this->get->id));
		
		$this->render('settings_addcustomfield.php');
	}
	
	
	public function addpirepfield() {
        $this->checkPermission(EDIT_PIREPS_FIELDS);
		$this->set('title', Lang::gs('pirep.field.add'));
		$this->set('action', 'addfield');
		$this->render('settings_addpirepfield.php');
	}
	
	public function editpirepfield() {
        $this->checkPermission(EDIT_PIREPS_FIELDS);
		$this->set('title', Lang::gs('pirep.field.edit'));
		$this->set('action', 'savefields');
		$this->set('field', PIREPData::GetFieldInfo($this->get->id));
		
		$this->render('settings_addpirepfield.php');
	}
	
	public function pirepfields() {
        $this->checkPermission(EDIT_PIREPS_FIELDS);

		switch($this->post->action) {
			case 'savefields':
				$this->PIREP_SaveFields();
				break;
				
			case 'addfield':
				$this->PIREP_AddField();
				break;
				
			case 'deletefield':
				$this->PIREP_DeleteField();
				break;
		}
		
		$this->PIREP_ShowFields();
	}
	
	public function customfields() {
        $this->checkPermission(EDIT_PROFILE_FIELDS);

		switch($this->post->action) {
			case 'savefield':
				$this->save_fields_post();
				break;
				
			case 'addfield':
				$this->add_field_post();
				break;
				
			case 'deletefield':
				$this->delete_field_post();
				return;
				break;
		}
		
		$this->ShowFields();
	}
	
	/* Utility functions */	
	
		
	protected function save_settings_post() {

		unset($_POST['action']);
		unset($_POST['submit']);

		while(list($name, $value) = each($_POST)){

			if($name == 'action')
					continue;
			elseif($name == 'submit')
				continue;
			
			$value = DB::escape($value);
			SettingsData::SaveSetting($name, $value, '', false);
		
		}
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Changed settings');
		
		$this->set('message', 'Settings were saved!');
		$this->render('core_success.php');
	}
	
	protected function add_field_post() {

		if($this->post->title == '') {
			echo 'No field name entered!';
			return;
		}
		
		$data = array(
			'title'=>$this->post->title,
			'value'=>$this->post->value,
			'type'=>$this->post->type,
			'public'=>$this->post->public,
			'showinregistration'=>$this->post->showinregistration
		);
			
		if($data['public'] == 'yes')
			$data['public'] = true;
		else
			$data['public'] = false;
			
		if($data['showinregistration'] == 'yes')
			$data['showinregistration'] = true;
		else
			$data['showinregistration'] = false;
			
		$ret = SettingsData::AddField($data);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error saving the settings: ' . DB::error());
			$this->render('core_error.php');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Added custom registration field "'.$this->post->title.'"');
			
			$this->set('message', 'Added custom registration field "'.$this->post->title.'"');
			$this->render('core_success.php');
		}
	}
	
	protected function save_fields_post() {

		if($this->post->title == '') {
			echo 'No field name entered!';
			return;
		}
		
		$data = array(
			'fieldid'=>$this->post->fieldid,
			 'title'=>$this->post->title,
			 'value'=>$this->post->value,
			 'type'=>$this->post->type,
			 'public'=>$this->post->public,
			 'showinregistration'=>$this->post->showinregistration
		);
		
		if($data['public'] == 'yes')
			$data['public'] = true;
		else
			$data['public'] = false;
			
		if($data['showinregistration'] == 'yes')
			$data['showinregistration'] = true;
		else
			$data['showinregistration'] = false;
		
		$ret = SettingsData::EditField($data);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error saving the settings: ' . DB::error());
			$this->render('core_error.php');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Edited custom registration field "'.$this->post->title.'"');
			
			$this->set('message', 'Edited custom registration field "'.$this->post->title.'"');
			$this->render('core_success.php');
		}
	}
	
	protected function delete_field_post() {

		$id = DB::escape($this->post->id);
		
		$ret = SettingsData::deleteField($id);
		if(DB::errno() != 0) {
			echo json_encode(array(
					'status' => 'error',
					'message' => addslashes(DB::error())
			) );
			
			return;
		}
		
		echo json_encode(array('status' => 'ok'));
	}
	
	protected function ShowSettings()
	{
		$this->set('allsettings', SettingsData::GetAllSettings());
		$this->render('settings_mainform.php');
	}
	
	protected function ShowFields() {

		$this->set('allfields', SettingsData::GetAllFields());
		$this->render('settings_customfieldsform.php');
	}
	
	protected function PIREP_ShowFields() {
		$this->set('allfields', PIREPData::GetAllFields());
		
		$this->render('settings_pirepfieldsform.php');
	}
	
	protected function PIREP_AddField() {

		if($this->post->title == '') {
			echo 'No field name entered!';
			return;
		}
		
		$ret = PIREPData::AddField($this->post->title, $this->post->type, $this->post->options);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error saving the field: ' . DB::error());
			$this->render('core_error.php');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Added PIREP field "'.$this->post->title.'"');
			
			$this->set('message', 'Added PIREP field "'.$this->post->title.'"');
			$this->render('core_success.php');
		}
	}
	
	protected function PIREP_SaveFields() {
		
		if($this->post->title == '') {
			$this->set('message', 'The title cannot be blank');
			$this->render('core_error.php');
			return false;
		}
		
		$res = PIREPData::EditField($this->post->fieldid, $this->post->title, $this->post->type, $this->post->options);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error saving the field');
			$this->render('core_error.php');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Edited PIREP field "'.$this->post->title.'"');
			
			$this->set('message', 'Edited PIREP field "'.$this->post->title.'"');
			$this->render('core_success.php');
		}		
	}
	
	protected function PIREP_DeleteField() {
		$id = DB::escape($this->post->id);
		
		$ret = PIREPData::DeleteField($id);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error deleting the field: ' . DB::$err);
			$this->render('core_error.php');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Deleted PIREP field');
			
			$this->set('message', 'The field was deleted');
			$this->render('core_success.php');
		}
	}
}