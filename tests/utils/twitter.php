<?php

include dirname(__FILE__).'/bootstrap.inc.php';

echo '<pre>';

Config::set('TWITTER_AIRLINE_ACCOUNT', 'nabeels');
Config::set('TWITTER_ENABLE_PUSH', false);
ActivityData::readTwitter();

// Push a test message to Twitter
Config::set('TWITTER_ENABLE_PUSH', true);

$pirep = PIREPData::getRecentReportsByCount(20);
$idx = rand(0, count($pirep) - 1);
$pirep = $pirep[$idx];

$message = Lang::get('activity.new.pirep');
foreach($pirep as $key=>$value) {
    $message = str_replace('$'.$key, $value, $message);
}
        
# Push a new PIREP
$ret = ActivityData::pushToTwitter(array(
    'pilotid' => $pirep->pilotid,
    'type' => ACTIVITY_NEW_PIREP,
    'refid' => $pirep->pirepid,
    'message' => $message,
));

var_dump($ret);