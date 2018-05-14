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
        'https://a.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            maxZoom: 15,
            noWrap: true,
            attribution: 'Map tiles by Carto, under CC BY 3.0. Data by OpenStreetMap, under ODbL.'
        });

    feature_groups.push(opencyclemap_phys_osm);

    const openaip_basemap_phys_osm = L.featureGroup(feature_groups);
    let map = L.map(opts.render_elem, {
        layers: [openaip_basemap_phys_osm],
        center: opts.center,
        zoom: opts.zoom,
        scrollWheelZoom: false,
    });

    return map
};