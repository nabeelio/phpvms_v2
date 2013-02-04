<?php
include dirname(__FILE__).'/bootstrap.inc.php';

include SITE_ROOT.'/install/includes/Installer.class.php';


echo '<pre>';

$_POST['TABLE_PREFIX'] = 'phpvms_';
$sql = Installer::readSQLFile(SITE_ROOT.'/install/sql/install.sql');

print_r($sql);