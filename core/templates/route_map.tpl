<h3>Route Map</h3>
<div class="mapcenter" align="center">
	<div id="routemap" style="width:<?php echo  Config::Get('MAP_WIDTH');?>; height: <?php echo Config::Get('MAP_HEIGHT')?>"></div>
</div>
<?php
/**
 * 
 * This is the new Google Maps v3 code. Be careful of changing
 * things here, only do something if you know what you're doing.
 * 	          
 * These are some options for the map, you can change here.
 * 
 * This map is used for schedules and PIREPS
 * 
 * By default, the zoom level and center are ignored, and the map 
 * will try to fit the all the flights in. If you want to manually set
 * the zoom level and center, set "autozoom" to false.
 * 
 * If you want to adjust the size of the map - Look at the above
 * "routemap" div with the CSS width/height parameters. You can 
 * easily adjust it from there.
 * 
 * And for reference, you want to tinker:
 * http://code.google.com/apis/maps/documentation/v3/basics.html
 */
 
if(isset($pirep))
	$mapdata = $pirep;
if(isset($schedule))
	$mapdata = $schedule;
?>
<?php
/*	This is a small template for information about a navpoint popup 
	
	Variables available:
	
	<%=nav.title%>
	<%=nav.name%>
	<%=nav.freq%>
	<%=nav.lat%>
	<%=nav.lng%>
	<%=nav.type%>	2=NDB 3=VOR 4=DME 5=FIX 6=TRACK
 */
?>
<script type="text/html" id="navpoint_bubble">
	<span style="font-size: 10px; text-align:left; width: 100%" align="left">
	<strong>Name: </strong><%=nav.title%> (<%=nav.name%>)<br />
	<strong>Type: </strong>
	<?php	/* Show the type of point */ ?>
	<% if(nav.type == 2) { %> NDB <% } %>
	<% if(nav.type == 3) { %> VOR <% } %>
	<% if(nav.type == 4) { %> DME <% } %>
	<% if(nav.type == 5) { %> FIX <% } %>
	<% if(nav.type == 6) { %> TRACK <% } %>
	<br />
	<?php	/* Only show frequency if it's not a 0*/ ?>
	<% if(nav.freq != 0) { %>
	<strong>Frequency: </strong><%=nav.freq%>
	<% } %>
	</span>
</script>

<?php
/*	Below here is all the javascript for the map. Be careful of what you
	modify!! */
?>
<script src="<?php echo SITE_URL?>/lib/js/base_map.js"></script>
<script type="text/javascript">

const map = createMap({
	render_elem: 'routemap',
});

const depCoords = L.latLng(<?php echo $mapdata->deplat?>, <?php echo $mapdata->deplng;?>);
const depMarker = L.marker(depCoords, {
	icon: L.icon({ iconUrl: depicon, iconSize: [35, 35] })
}).bindPopup("<?php echo $mapdata->depname;?>").addTo(map);

const arrCoords = L.latLng(<?php echo $mapdata->arrlat?>, <?php echo $mapdata->arrlng;?>);
const arrMarker = L.marker(arrCoords, {
	icon: L.icon({ iconUrl: arricon, iconSize: [35, 35] })
}).bindPopup("<?php echo $mapdata->arrname;?>").addTo(map);

const icon_vor = L.icon({ 
	iconUrl: "<?php echo fileurl('/lib/images/icon_vor.png') ?>",
	iconSize: [19, 20],
})
const icon_fix = L.icon({ 
	iconUrl: "<?php echo fileurl('/lib/images/icon_fix.png') ?>",
	iconSize: [12, 15],
})

// for drawing the line
let points = [];
points.push(depCoords);

<?php
if(is_array($mapdata->route_details)) {
	foreach($mapdata->route_details as $route) {
		if($route->type == NAV_VOR)
			$icon = 'icon_vor';
		else
			$icon = 'icon_fix';
		
		//	Build info array for the bubble
		?>
		const v<?php echo $route->name?>_info = {
			freq: "<?php echo $route->freq ?>",
			name: "<?php echo $route->name ?>",
			title: "<?php echo $route->title ?>",
			type: "<?php echo $route->type ?>",
			lat: "<?php echo $route->lat ?>",
			lng: "<?php echo $route->lng ?>"
		};
		
		const <?php echo $route->name?>_point = L.latLng(<?php echo $route->lat?>, <?php echo $route->lng?>);
		points.push(<?php echo $route->name?>_point);

		L.marker(<?php echo $route->name?>_point, {
			icon: <?php echo $icon ?>,
			title: "<?php echo $route->title; ?>",
		})
		.bindPopup(tmpl("navpoint_bubble", {nav: v<?php echo $route->name?>_info}))
		.addTo(map);
		<?php
	}
}
?>

points.push(arrCoords);

const geodesicLayer = L.geodesic([points], {
	weight: 3,
	opacity: 0.5,
	color: 'black',
	steps: 10
}).addTo(map);

map.fitBounds(geodesicLayer.getBounds());

</script>
