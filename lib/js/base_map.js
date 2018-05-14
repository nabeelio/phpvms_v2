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
        provider: 'OpenStreetMap.Mapnik',
    }, opts);

    let map = L.map(opts.render_elem, {
        center: opts.center,
        zoom: opts.zoom,
        scrollWheelZoom: false,
    });

    // Want to customizer the basemap? Look at the leaflet-providers page:
    //  http://leaflet-extras.github.io/leaflet-providers/preview/
    // Just set the provider name that you want below, or add additional
    // Some providers require an API key, so just look out for that
    L.tileLayer.provider(opts.provider).addTo(map);

    return map
};