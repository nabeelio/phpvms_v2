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

		switch(self::$controller->function) {
			case '':
			case 'settings':
				$this->set('sidebar', 'sidebar_settings.tpl');
				break;
		
			case 'customfields':
				$this->set('sidebar', 'sidebar_customfields.tpl');
				break;
				
			case 'pirepfields':
				$this->set('sidebar', 'sidebar_pirepfields.tpl');
				break;
		}
	}
	
	public function index() {
		$this->settings();
	}
	
	public function settings() {

		if(isset(self::$post->action)) {
			switch(self::$post->action) {
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
		$this->set('title', Lang::gs('settings.add.field'));
		$this->set('action', 'addfield');
		
		$this->render('settings_addcustomfield.tpl');
	}
	
	public function editfield() {
		$this->set('title', Lang::gs('settings.edit.field'));
		$this->set('action', 'savefield');
		$this->set('field', SettingsData::GetField(self::$get->id));
		
		$this->render('settings_addcustomfield.tpl');
	}
	
	
	public function addpirepfield() {
		$this->set('title', Lang::gs('pirep.field.add'));
		$this->set('action', 'addfield');
		$this->render('settings_addpirepfield.tpl');
	}
	
	public function editpirepfield() {
		$this->set('title', Lang::gs('pirep.field.edit'));
		$this->set('action', 'savefields');
		$this->set('field', PIREPData::GetFieldInfo(self::$get->id));
		
		$this->render('settings_addpirepfield.tpl');
	}
	
	public function pirepfields() {

		switch(self::$post->action) {
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

		switch(self::$post->action) {
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
		$this->render('core_success.tpl');
	}
	
	protected function add_field_post() {

		if(self::$post->title == '') {
			echo 'No field name entered!';
			return;
		}
		
		$data = array(
			'title'=>self::$post->title,
			'value'=>self::$post->value,
			'type'=>self::$post->type,
			'public'=>self::$post->public,
			'showinregistration'=>self::$post->showinregistration
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
			$this->render('core_error.tpl');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Added custom registration field "'.self::$post->title.'"');
			
			$this->set('message', 'Added custom registration field "'.self::$post->title.'"');
			$this->render('core_success.tpl');
		}
	}
	
	protected function save_fields_post() {

		if(self::$post->title == '') {
			echo 'No field name entered!';
			return;
		}
		
		$data = array(
			'fieldid'=>self::$post->fieldid,
			 'title'=>self::$post->title,
			 'value'=>self::$post->value,
			 'type'=>self::$post->type,
			 'public'=>self::$post->public,
			 'showinregistration'=>self::$post->showinregistration
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
			$this->render('core_error.tpl');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Edited custom registration field "'.self::$post->title.'"');
			
			$this->set('message', 'Edited custom registration field "'.self::$post->title.'"');
			$this->render('core_success.tpl');
		}
	}
	
	protected function delete_field_post() {

		$id = DB::escape(self::$post->id);
		
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
		$this->render('settings_mainform.tpl');
	}
	
	protected function ShowFields() {

		$this->set('allfields', SettingsData::GetAllFields());
		$this->render('settings_customfieldsform.tpl');
	}
	
	protected function PIREP_ShowFields() {
		$this->set('allfields', PIREPData::GetAllFields());
		
		$this->render('settings_pirepfieldsform.tpl');
	}
	
	protected function PIREP_AddField() {

		if(self::$post->title == '') {
			echo 'No field name entered!';
			return;
		}
		
		$ret = PIREPData::AddField(self::$post->title, self::$post->type, self::$post->options);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error saving the field: ' . DB::error());
			$this->render('core_error.tpl');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Added PIREP field "'.self::$post->title.'"');
			
			$this->set('message', 'Added PIREP field "'.self::$post->title.'"');
			$this->render('core_success.tpl');
		}
	}
	
	protected function PIREP_SaveFields() {
		
		if(self::$post->title == '') {
			$this->set('message', 'The title cannot be blank');
			$this->render('core_error.tpl');
			return false;
		}
		
		$res = PIREPData::EditField(self::$post->fieldid, self::$post->title, self::$post->type, self::$post->options);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error saving the field');
			$this->render('core_error.tpl');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Edited PIREP field "'.self::$post->title.'"');
			
			$this->set('message', 'Edited PIREP field "'.self::$post->title.'"');
			$this->render('core_success.tpl');
		}		
	}
	
	protected function PIREP_DeleteField() {
		$id = DB::escape(self::$post->id);
		
		$ret = PIREPData::DeleteField($id);
		
		if(DB::errno() != 0) {
			$this->set('message', 'There was an error deleting the field: ' . DB::$err);
			$this->render('core_error.tpl');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Deleted PIREP field');
			
			$this->set('message', 'The field was deleted');
			$this->render('core_success.tpl');
		}
	}
}