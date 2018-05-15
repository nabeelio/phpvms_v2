/**
 * Basemap for leaflet
 */

/**
 * Elements used across maps
 */
const MapFeatures = {
    icons: {
        departure: L.icon({ iconUrl: depicon, iconSize: [35, 35] }),
        arrival: L.icon({ iconUrl: arricon, iconSize: [35, 35] }),
        vor: L.icon({
            iconUrl: url + "/lib/images/icon_vor.png",
            iconSize: [19, 20],
        }),
        fix: L.icon({
            iconUrl: url + "/lib/images/icon_fix.png",
            iconSize: [12, 15],
        }),
    },
};

/**
 * Create a new map onto a given element. If you have or want to add
 * any Leaflet plugins, just apply them here above the return statement
 * They'll be applied to both the ACARS and PIREP maps
 * @param {} opts Options to override
 */
const createMap = (opts) => {
    opts = Object.assign({
        render_elem: 'map',
        center: [29.98139, -95.33374],
        zoom: 5,
        maxZoom: 10,
        layers: [],
        set_marker: false,
        provider: 'OpenStreetMap.Mapnik',
    }, opts);

    let map = L.map(opts.render_elem, {
        center: opts.center,
        zoom: opts.zoom,
        scrollWheelZoom: true,
    });

    // Want to customizer the basemap? Look at the leaflet-providers page:
    //  http://leaflet-extras.github.io/leaflet-providers/preview/
    // Just set the provider name that you want below, or add additional
    // Some providers require an API key, so just look out for that
    L.tileLayer.provider(opts.provider).addTo(map);

    return map;
};