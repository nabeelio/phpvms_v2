/**
 * Basemap for leaflet
 */

const createMap = (opts) => {

    opts = Object.assign({
        render_elem: 'map',
        center: [29.98139, -95.33374],
        zoom: 5,
        maxZoom: 10,
        layers: [],
        set_marker: false,
    }, opts);

    let feature_groups = [];
    const opencyclemap_phys_osm = new L.TileLayer(
        'http://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey=f09a38fa87514de4890fc96e7fe8ecb1', {
            maxZoom: 14,
            minZoom: 4,
            format: 'image/png',
            transparent: true
        });

    feature_groups.push(opencyclemap_phys_osm);

    const openaip_basemap_phys_osm = L.featureGroup(feature_groups);
    let map = L.map(opts.render_elem, {
        layers: [openaip_basemap_phys_osm],
        center: opts.center,
        zoom: opts.zoom,
        scrollWheelZoom: false,
    });

    const attrib = L.control.attribution({ position: 'bottomleft' });
    attrib.addAttribution('<a href="https://www.thunderforest.com" target="_blank" style="">Thunderforest</a>');
    attrib.addAttribution('<a href="https://www.openaip.net" target="_blank" style="">openAIP</a>');
    attrib.addAttribution('<a href="https://www.openstreetmap.org/copyright" target="_blank" style="">OpenStreetMap</a> contributors');
    attrib.addAttribution('<a href="https://www.openweathermap.org" target="_blank" style="">OpenWeatherMap</a>');

    attrib.addTo(map);

    return map
};