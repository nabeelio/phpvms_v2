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
 
define('ADMIN_PANEL', true);
include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.inc.php';


Template::Show('header');

// Controller
switch($_GET['page']) {
    
	case 'dbsetup':
	case '':
		
		if(!Installer::CheckServer()) {
			Template::Show('s0_config_check');
		} else {
			Template::Show('s1_db_setup');
		}
		
		break;
		
	case 'installdb':
	
		if($_POST['action'] == 'submitdb') {
		  
			if($_POST['DBASE_NAME'] == '' || $_POST['DBASE_USER'] == '' || $_POST['DBASE_TYPE'] == ''
				|| $_POST['DBASE_SERVER'] == '' || $_POST['SITE_URL'] == '') {
				echo '<div id="error">You must fill out all the required fields</div>';
				break;
			}
		
			if(!Installer::addTables())	{
				echo '<div id="error">'.Installer::$error.'</div>';
				break;
			}
			
			if(!Installer::writeConfig()) {
				echo '<div id="error">'.Installer::$error.'</div>';
				break;
			}
			
			SettingsData::saveSetting('PHPVMS_VERSION', INSTALLER_VERSION);

			echo '<div align="center" style="font-size: 18px;"><br />
					<a href="install.php?page=sitesetup">Continue to the next step</a>
				  </div>';	
		}
		
		break;
		
	case 'sitesetup':
		
		Template::Show('s2_site_setup');
		break;
		
	case 'complete':
		
		if($_POST['action'] == 'submitsetup') {
		  
			if($_POST['firstname'] == '' || $_POST['lastname'] == '' 
				|| $_POST['email'] == '' ||  $_POST['password'] == '' || $_POST['vaname'] == '' 
				|| $_POST['vacode'] == '') {
				    
				Template::Set('message', 'You must fill out all of the fields');
				Template::Show('s2_site_setup');
				break;
			}
			
			$_POST['SITE_NAME'] = $_POST['vaname'];
				
			if(!Installer::SiteSetup()) {
				Template::Set('message', Installer::$error);
				Template::Show('s2_site_setup');
			} else {
				Installer::RegisterInstall(INSTALLER_VERSION);
				Template::Show('s3_setup_finished');
			}
		}
		
		break;
}	

Template::Show('footer');
?>
