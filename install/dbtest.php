<?php

include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.inc.php';

if(!DB::init($_POST['DBASE_TYPE'])) {
	Template::Set('message', 'There was an error initializing the database');
	Template::Show('error');
	return false;
}
try{
    $ret = @DB::connect($_POST['DBASE_USER'], $_POST['DBASE_PASS'], $_POST['DBASE_NAME'], $_POST['DBASE_SERVER']);
}catch(exception $e){
	Template::Set('message', 'Error: '.$e->error);
	Template::Show('error');
    return false;
}
/*
if($ret == false) {
	Template::Set('message', DB::error());
	Template::Show('error');
	return false;
}
*/
/** I dont know why this is here, Help me out guys. */
if(!DB::select($_POST['DBASE_NAME'])) {
	Template::Set('message', DB::error());
	Template::Show('error');
	return false;
}
/** =============================================== */

Template::Set('message', 'Database connection is ok!');
Template::Show('success');
