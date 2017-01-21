<h3>Route Map</h3>
<div class="mapcenter" align="center">
	<div id="routemap" style="width:600px; height: 480px"></div>
</div>
<p><strong>Route: </strong><?php echo $mapdata->route;?></p>
<script type="text/javascript">
var options = {
	mapTypeId: google.maps.MapTypeId.ROADMAP,
	disableDefaultUI: true
}

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

var flightPath = new google.maps.Polyline({
	path: [dep_location, <?php if(count($list) > 0) { echo implode(',', $list).','; }?> arr_location],
	strokeColor: "#FF0000", strokeOpacity: 1.0, strokeWeight: 2
}).setMap(map);

map.fitBounds(bounds); 
</script>