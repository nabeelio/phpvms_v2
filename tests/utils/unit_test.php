<?php

include 'core/codon.config.php';
error_reporting(E_ALL);
ini_set('display_errors', 'on');
echo '<pre>';
CodonCache::setStatus(false);


$cws = new CodonWebService();
$xml = $cws->get('http://www.vacentral.net/airline/cva_canadianvirtualairlines/xml');

$xml = simplexml_load_string($xml);

echo "Our airline's rank is {$xml->rank}";

print_r($_POST);

echo 'set names<br>';
//$results = DB::query("SET NAMES 'utf8'");
DB::debug();

$params = array(
	'column' => 'value',
	'dateadded = CURDATE()',
	'fruit' => array(
		'apples',
		'oranges',
		'grapes',
	),
);

echo DB::build_where($params);

echo 'schedules<br>';
echo DB::build_where(array('params' => array('value1',)));
//$results = DB::query('SELECT * FROM phpvms_schedules');
//$results = DB::query("SET NAMES 'utf8'");
?>

<form action="" method="post">
	<input type="hidden" name="name" value="Omega-Air" />
	<input type="submit" name="submit" value="Submit" />
</form>

