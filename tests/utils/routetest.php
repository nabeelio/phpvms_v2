<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

include '../core/codon.config.php';
?>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="<?php echo fileurl('lib/js/jquery.form.js');?>"></script>
<script type="text/javascript" src="<?php echo fileurl('lib/js/jquery-ui.js');?>"></script>
<script type="text/javascript" src="<?php echo fileurl('lib/js/phpvms.js');?>"></script>
</head>
<body>
<form method="get" action="">
	Dep: <input type ="text" name="depicao" value="<?php echo $_GET['depicao'];?>" /> 
	Arr: <input type ="text" name="arricao" value="<?php echo $_GET['arricao'];?>" /><br />
	Route:<br />
	<textarea name="route" style="width: 600px; height: 100px;"><?php echo $_GET['route'];?></textarea>
	<br />
	<input type="submit" name="submit" value="View Route" />
</form>
<pre>
<?php
$data = new stdClass();
$data->route = $_GET['route'];

$depicao = OperationsData::getAirportInfo($_GET['depicao']);
if(!$depicao)
{ 
	$depicao = OperationsData::RetrieveAirportInfo($_GET['depicao']);
}

$arricao = OperationsData::getAirportInfo($_GET['arricao']);

if(!$arricao)
{
	 $arricao = OperationsData::RetrieveAirportInfo($_GET['arricao']);
}

$data->deplat = $depicao->lat;
$data->deplng = $depicao->lng;
$data->depname = $depicao->name;

$data->arrlat = $arricao->lat;
$data->arrlng = $arricao->lng;
$data->arrname = $arricao->name;

unset($depicao);
unset($arricao);

$data->route_details = NavData::parseRoute($data);

print_r($data->route_details);
$mapdata = $data;
?>
</pre>
<h4>Route Map</h4>
<div class="mapcenter" align="center">
	<div id="routemap" style="width:600px; height: 480px"></div>
</div>
<p><strong>Route: </strong><?php echo $mapdata->route;?></p>
<h4>Debugging Info</h4>
<pre>
<?php echo print_r($mapdata->route_details); ?>
</pre>
<script type="text/javascript">
var options = {
	mapTypeId: google.maps.MapTypeId.ROADMAP
};

var map = new google.maps.Map(document.getElementById("routemap"), options);

var dep_location = new google.maps.LatLng(<?php echo $mapdata->deplat?>, <?php echo $mapdata->deplng;?>);
var arr_location = new google.maps.LatLng(<?php echo $mapdata->arrlat?>, <?php echo $mapdata->arrlng;?>);

// Resize the view to fit it all in
var bounds = new google.maps.LatLngBounds();
bounds.extend(dep_location);
bounds.extend(arr_location);

var depMarker = new google.maps.Marker({
	position: dep_location,
	map: map,
	icon: depicon,
	title: "<?php echo $mapdata->depname;?>"
});
<?php
/* Populate the route */
if(is_array($mapdata->route_details))
{
	$list = array();
	
	foreach($mapdata->route_details as $route)
	{
		if($route->type == NAV_VOR)
		{
			$icon = fileurl('/lib/images/icon_vor.png');
		}
		else
		{
			$icon = fileurl('/lib/images/icon_fix.png');
		}
		
		echo 'var loc = new google.maps.LatLng('.$route->lat.', '.$route->lng.');
var _marker = new google.maps.Marker({
	position: loc,
	map: map,
	icon: "'.$icon.'",
	title: "'.$route->title.'"
});

bounds.extend(loc);';
		
		// For the polyline
		$list[] = "new google.maps.LatLng({$route->lat}, {$route->lng})";
	}
}
?>
var arrMarker = new google.maps.Marker({
	position: arr_location,
	map: map,
	icon: arricon,
	title: "<?php echo $mapdata->arrname;?>"
});

var flightPath = new google.maps.Polyline({path: [dep_location, <?php if(is_array($list)) { echo implode(',', $list).','; }?> arr_location], strokeColor: "#FF0000", strokeOpacity: 1.0, strokeWeight: 2}).setMap(map);

map.fitBounds(bounds); 
</script>
</body>
</html>