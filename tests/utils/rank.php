<?php


include dirname(__FILE__).'/bootstrap.inc.php';
echo '<pre>';

$pilot = PilotData::findPilots(array('p.totalflights > 0'));
$idx = rand(0, count($pilot) - 1);
$pilot = $pilot[$idx];

RanksData::calculateUpdatePilotRank($pilot->pilotid);