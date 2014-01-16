<?php

include 'loader.inc.php';

$requiredParams = array('DBASE_USER' => 'Database Username', 'DBASE_NAME' => 'Database Name', 'DBASE_SERVER' => 'Database Server');

foreach($requiredParams as $k => $v) {

    if(empty($_POST[$k])) {
        Template::Set('message', 'You have not provided ' . $v . '. Please check and try again.');
        Template::Show('error.tpl');
        return false;
    }
}

if(!DB::init($_POST['DBASE_TYPE']))
{
	Template::Set('message', 'There was an error initializing the database');
	Template::Show('error.tpl');
	return false;
}

try {
    $ret = DB::connect($_POST['DBASE_USER'], $_POST['DBASE_PASS'], $_POST['DBASE_NAME'], $_POST['DBASE_SERVER']);
} catch (Exception $e) {
    Template::Set('message', 'Invalid connection details. Please check and try again');
    Template::Show('error.tpl');
    return false;
}


try {
    DB::select($_POST['DBASE_NAME']);
} catch(Exception $e) {
    Template::Set('message', 'Invalid connection details. Please check and try again');
    Template::Show('error.tpl');
    return false;
}

Template::Set('message', 'Database connection is ok!');
Template::Show('success.tpl');
