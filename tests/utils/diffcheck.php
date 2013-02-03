<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

define('SITE_ROOT', dirname(dirname(dirname(__FILE__))));

include SITE_ROOT.'/core/codon.config.php';
include SITE_ROOT.'/core/lib/mysqldiff/MySQLDiff.class.php';

$params = array(
    'dbuser' => DBASE_USER,
    'dbpass' => DBASE_PASS,
    'dbname' => DBASE_NAME,
    'dbhost' => DBASE_SERVER,
    'dumpxml' => SITE_ROOT.'/install/sql/structure.xml',
);

echo '<pre>';
    
try {
    $diff = new MySQLDiff($params); 
} catch(Exception $e) {
    echo $e->getMessage(); 
}

try {
    $diff_lines = $diff->getDiffs();
} catch(Exception $e) {
    echo $e->getMessage(); 
}

var_dump($diff_lines);

# This returns an array of what's missing in the database
try {
    $diff_lines = $diff->getSQLDiffs();
} catch(Exception $e) {
    echo $e->getMessage(); 
}

print_r($diff_lines);