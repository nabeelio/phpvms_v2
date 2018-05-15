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
 *
 * Rewritten for Google Maps v3
 */

/**
 * 
 */
function renderAcarsMap(opts) {

    let bounds = [];

    let selPoints = [],
        selMarkers = [];
    let selDepMarker, selArrMarker, selPointsLayer;

    let flightMarkers = [];
    let headingIcons = {};

    let info_window = null;
    let run_once = false;

    opts = Object.assign({
        render_elem: 'routemap',
        provider: 'OpenStreetMap.Mapnik',
        autozoom: true,
        zoom: 5,
        center: L.latLng(-25.363882, 131.044922),
        refreshTime: 12000,
        autorefresh: true
    }, opts);

    const map = createMap(opts);

    /**
     * Get the marker for a specific heading
     * @param {*} heading 
     */
    const getHeadingIcon = (heading) => {
        if (!(heading in headingIcons)) {
            headingIcons[heading] = L.icon({
                iconUrl: url + "/lib/images/inair/" + heading + ".png",
                iconSize: [35, 35]
            });
        }

        return headingIcons[heading];
    };

    /**
     * Clear all of the markers and selected points
     */
    const clearSelMarkers = () => {
        if (selDepMarker) {
            selDepMarker.remove();
            selDepMarker = null;
        }

        if (selArrMarker) {
            selArrMarker.remove();
            selArrMarker = null;
        }

        if (selPointsLayer) {
            selPointsLayer.remove();
            selPointsLayer = null;
        }

        for (let i in selMarkers) {
            selMarkers[i].remove();
        }

        selPoints = [];
    };

    /**
     * Draw the points/route for a flight
     * @param {*} features 
     */
    const flightClick = (flight) => {

        clearSelMarkers();

        const depCoords = L.latLng(flight.deplat, flight.deplng);
        selDepMarker = L.marker(depCoords, {
            icon: MapFeatures.icons.departure,
        }).addTo(map);

        const arrCoords = L.latLng(flight.arrlat, flight.arrlng);
        selArrMarker = L.marker(arrCoords, {
            icon: MapFeatures.icons.arrival,
        }).addTo(map);

        selPoints.push(depCoords);

        console.log(flight);

        $.each(flight.route_details, function(i, nav) {
            const loc = L.latLng(nav.lat, nav.lng);
            const icon = (nav.type === 3) ? MapFeatures.icons.vor : MapFeatures.icons.fix;
            selPoints.push(loc);

            const marker = L.marker(loc, {
                    icon: icon,
                    title: nav.title,
                })
                .bindPopup(tmpl("navpoint_bubble", { nav: nav }))
                .addTo(map);

            selMarkers.push(marker);
        });

        selPoints.push(arrCoords);

        selPointsLayer = L.geodesic([selPoints], {
            weight: 2,
            opacity: 1.0,
            color: '#FF0000',
            steps: 10
        }).addTo(map);

        map.fitBounds(selPointsLayer.getBounds());
    };

    /**
     * 
     * @param {*} data 
     */
    const populateMap = (data) => {

        clearMap();

        $("#pilotlist").html("");

        if (data.length == 0) {
            return false;
        }

        let lat, lng;
        let details, row, pilotlink;

        bounds = [];

        $.each(data, function(i, flight) {
            if (flight == null || flight.lat == null || flight.lng == null ||
                flight.lat == "" || flight.lng == "") {
                return;
            }

            flight.lat = Number(flight.lat);
            flight.lng = Number(flight.lng);

            lat = flight.lat;
            lng = flight.lng;

            if (i % 2 == 0)
                flight.trclass = "even";
            else
                flight.trclass = "odd";

            // Pull ze templates!
            const map_row = tmpl("acars_map_row", { flight: flight });
            const detailed_bubble = tmpl("acars_map_bubble", { flight: flight });

            $('#pilotlist').append(map_row);

            const pos = L.latLng(lat, lng);
            const marker = L.marker(pos, {
                    icon: getHeadingIcon(flight.heading)
                })
                .on('click', (e) => {
                    flightClick(flight);
                })
                .bindPopup(detailed_bubble)
                .addTo(map);

            flightMarkers.push(marker);
            bounds.push(pos);
        });

        // If they selected autozoom, only do the zoom first time
        if (opts.autozoom == true && run_once == false) {
            map.fitBounds(bounds);
            run_once = true;
        }
    }

    /**
     * Clear all markers and layers
     */
    const clearMap = () => {
        // clear markers
        for (let i in flightMarkers) {
            flightMarkers[i].remove();
        }
    };

    /**
     * 
     */
    const liveRefresh = () => {
        $.ajax({
            type: "GET",
            url: url + "/action.php/acars/data",
            dataType: "json",
            cache: false,
            success: function(data) {
                populateMap(data);
            }
        });
    };

    /**
     * Render
     * 
     */
    liveRefresh();
    if (opts.autorefresh == true) {
        setInterval(function() { liveRefresh(); }, opts.refreshTime);
    }
}