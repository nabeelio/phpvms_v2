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
 
/**
 * phpVMS Installer File
 *  "Boot" file includes our basic "needs" for the installer
 */
 
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');

define('DS', DIRECTORY_SEPARATOR);
define('SITE_ROOT', dirname(dirname(dirname(__FILE__))));
define('CORE_PATH', SITE_ROOT . DS . 'core');
define('CLASS_PATH', CORE_PATH . DS . 'classes');
define('INSTALL_ROOT', SITE_ROOT . DS . 'install');

if(!file_exists(CORE_PATH.DS.'local.config.php') || filesize(CORE_PATH.DS.'local.config.php') == 0) {
	
	/* Include just some basic files to get the install going */
	include CLASS_PATH . DS . 'ezdb' . DS . 'ezdb.class.php';
	include CLASS_PATH . DS . 'CodonCache.class.php';
	include CLASS_PATH . DS . 'CodonData.class.php';
	include CLASS_PATH . DS . 'Config.class.php';
	include CLASS_PATH . DS . 'Debug.class.php';
	include CLASS_PATH . DS . 'Template.class.php';
	include CLASS_PATH . DS . 'TemplateSet.class.php';
	include CORE_PATH . DS . 'common' . DS . 'SettingsData.class.php';
    
} else {
	include CORE_PATH . DS . 'codon.config.php';
}

include CORE_PATH.DS.'lib' . DS . 'mysqldiff' . DS . 'MySQLDiff.class.php';
include INSTALL_ROOT.DS.'includes' . DS . 'Installer.class.php';

Template::init();
Template::setTemplatePath(INSTALL_ROOT.DS.'templates');

# Get the version info from the version file
$revision = file_get_contents(CORE_PATH.DS.'version');

preg_match('/^[v]?(.*)-([0-9]*)-(.*)/', $revision, $matches);
list($FULL_VERSION_STRING, $full_version, $revision_count, $hash) = $matches;

preg_match('/([0-9]*)\.([0-9]*)\.([0-9]*)/', $full_version, $matches);
list($full, $major, $minor, $revision) = $matches;

define('MAJOR_VERSION', $major.'.'.$minor);
define('INSTALLER_FULL_VERSION', $FULL_VERSION_STRING);
define('INSTALLER_VERSION', $full_version);
define('UPDATE_VERSION', $full_version);
define('REVISION', $revision);
?>
