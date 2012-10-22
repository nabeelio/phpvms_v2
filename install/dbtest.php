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

include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.inc.php';

if(!DB::init($_POST['DBASE_TYPE'])) {
	Template::Set('message', 'There was an error initializing the database');
	Template::Show('error');
	return false;
}

$ret = DB::connect($_POST['DBASE_USER'], $_POST['DBASE_PASS'], $_POST['DBASE_NAME'], $_POST['DBASE_SERVER']);

if($ret == false) {
	Template::Set('message', DB::error());
	Template::Show('error');
	return false;
}

if(!DB::select($_POST['DBASE_NAME'])) {
	Template::Set('message', DB::error());
	Template::Show('error');
	return false;
}

Template::Set('message', 'Database connection is ok!');
Template::Show('success');
?>
