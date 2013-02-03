<?php

include dirname(__FILE__).'/bootstrap.inc.php';

echo '<pre>';

$sql = 'UPDATE `'.TABLE_PREFIX.'pireps` SET `accepted`='.PIREP_PENDING.' WHERE `pirepid`=2';
DB::query($sql);

PIREPData::changePIREPStatus(2, PIREP_ACCEPTED);

var_dump(LedgerData::getPaymentByPIREP(2));
