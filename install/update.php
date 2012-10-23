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

$CURRENT_VERSION = SettingsData::getSetting('PHPVMS_VERSION');
if(!$CURRENT_VERSION) {
	$_GET['force'] = true;
} else {
    
	$CURRENT_VERSION = $CURRENT_VERSION->value;
    
    if(substr_count($CURRENT_VERSION, '-')) {
        preg_match('/^[v]?(.*)-([0-9]*)-(.*)/', $CURRENT_VERSION, $matches);
        list($CURR_FULL_VERSION_STRING, $curr_full_version, $curr_revision_count, $curr_hash) = $matches;
        
        preg_match('/([0-9]*)\.([0-9]*)\.([0-9]*)/', $full_version, $matches);
        list($CURR_FULL_VERSION, $curr_major, $curr_minor, $curr_revision) = $matches;
        
        $CURRENT_VERSION = $curr_major.'.'.$curr_minor.'.'.$curr_revision;
    }
}

$CURRENT_VERSION = str_replace('.', '', $CURRENT_VERSION);

Template::SetTemplatePath(SITE_ROOT.DS.'install'.DS.'templates');
Template::Show('header');

# Ew
echo '<h3 align="left">phpVMS Updater</h3>';

# Check versions for mismatch, unless ?force is passed
if(!isset($_GET['force']) && !isset($_GET['test'])) {
	if($CURRENT_VERSION == UPDATE_VERSION) {
		echo '<p>You already have updated! Please delete this /install folder.<br /><br />
				To force the update to run again, click: <a href="update.php?force">update.php?force</a></p>';
		
		Template::Show('footer');
		exit;
	}
}

/** 
 * Run a sql file
 */
// Do the queries:
echo 'Starting the update...<br />';

	# Do updates based on version
#	But cascade the updates

$CURRENT_VERSION = intval(str_replace('.', '', $CURRENT_VERSION));
$latestversion = intval(str_replace('.', '', UPDATE_VERSION));
    
$mysqlDiff = new MySQLDiff(array(
    'dbuser' => DBASE_USER,
    'dbpass' => DBASE_PASS,
    'dbname' => DBASE_NAME,
    'dbhost' => DBASE_SERVER,
    'dumpxml' => 'sql/structure.xml',
    )
);

$diffs_done = $mysqlDiff->getSQLDiffs();
if(!is_array($diffs_done)) {
    $diffs_done = array();
}

# Run it local so it's logged
foreach($diffs_done as $sql) {
    DB::query($sql);
}

/* Run the update fixtures file */
echo '<h2>Populating Update Data...</h2>';
$sqlLines = Installer::readSQLFile(SITE_ROOT.DS.'install'.DS.'fixtures'.DS.'update.sql', TABLE_PREFIX);
foreach($sqlLines as $sql) {
    DB::query($sql['sql']);
    if(DB::errno() != 0 && DB::errno() != 1062) {
        echo '<div id="error" style="text-align: left;">Writing to "'.$sql['table'].'" table... ';
        echo "<br /><br />".DB::error();
        echo '</div>';
    }
}

OperationsData::updateAircraftRankLevels();

/* Add them to the default group */
$status_type_list = Config::get('PILOT_STATUS_TYPES');
$pilot_list = PilotData::getAllPilots();
foreach($pilot_list as $pilot) {
    
    echo "Fixing settings for ".$pilot->firstname." ".$pilot->lastname."<br>";
    
    PilotData::resetLedgerforPilot($pilot->pilotid);
	PilotGroups::addUsertoGroup($pilot->pilotid, DEFAULT_GROUP);
    
    # Reset the default groups
    $status = $status_type_list[$pilot->retired];
    foreach($status['group_add'] as $group) {
        PilotGroups::addUsertoGroup($pilot->pilotid, $group);
    }
    
    foreach($status['group_remove'] as $group) {
        PilotGroups::removeUserFromGroup($pilot->pilotid, $group);
    }
}

SettingsData::saveSetting('PHPVMS_VERSION', $FULL_VERSION_STRING);

# Don't count forced updates
if(!isset($_GET['force'])) {
	Installer::RegisterInstall($FULL_VERSION_STRING);
}

echo '<p><strong>Update completed!</strong></p>
		<hr>
	  <p >If there were any errors, you may have to manually run the SQL update, 
		or correct the errors, and click the following to re-run the update: <br />
		<a href="update.php?force">Click here to force the update to run again</a></p>
	  <p>Click here to <a href="'.SITE_URL.'">goto your site</a>, or <a href="'.SITE_URL.'/admin">your admin panel</a></p>  ';

Template::Show('footer');