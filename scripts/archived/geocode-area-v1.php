<?php

if ( $_GET ) :

    require_once( 'con.php' );
    if ( ! isset( $_GET['type'] ) ||  ! isset( $_GET['type'] ) ) {
        die(json_encode(array('message' => 'ERROR', 'code' => 500)));
    }

    $type = $_GET['type'];
    $value =  $_GET['value'];

    if ( $type === 'grid_id' ) {
        $query = mysqli_query( $con, "
            SELECT *
            FROM {$tables['geonames']} as g
            WHERE g.parent_id = {$value} OR g.grid_id = {$value};
        " );
        if ( $query === false ) {
            die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
        }
        $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

        header('Content-type: application/json');

        echo json_encode($result);
    }

    if ( $type === 'states' ) {
        $query = mysqli_query( $con, "
            SELECT *
            FROM {$tables['geonames']} as g
            WHERE g.country_code = '{$value}' AND g.level = 1;
        " );
        if ( $query === false ) {
            die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
        }
        $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

        header('Content-type: application/json');

        echo json_encode($result);
    }

    if ( $type === 'counties' ) {
        $query = mysqli_query( $con, "
            SELECT *
            FROM {$tables['geonames']} as g
            WHERE g.country_code = '{$value}' AND g.level = 2;
        " );
        if ( $query === false ) {
            die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
        }
        $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

        header('Content-type: application/json');

        echo json_encode($result);
    }

    if ( $type === 'continent' ) {
        $query = mysqli_query( $con, "
            SELECT c.*
            FROM {$tables['geonames']} as c
            WHERE c.parent_id IN (
                SELECT g.grid_id
                FROM {$tables['geonames']} as g
                WHERE g.parent_id = {$value}
            );
        " );
        if ( $query === false ) {
            die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
        }
        $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

        header('Content-type: application/json');

        echo json_encode($result);
    }

    return;

endif; // html


/**
 * Below is the mapbox HTML page
 * The mapbox forms self reference this page.
 */
?>
<html>
<head>
    <meta charset='utf-8' />
    <title>Sample Bounding Box</title>
    <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.1.0/mapbox-gl.js'></script>
    <script src='geonames-mapbox.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.1.0/mapbox-gl.css' rel='stylesheet' />
    <style>
        body { margin:0; padding:0; }
        #map { position:absolute; top:0; bottom:0; width:100%; z-index: 10; }
        #info_box_container {
            position: absolute;
            top:20px;
            left:20px;
            bottom:20px;
            width:400px;

            z-index: 100;
            padding:30px;
            background-color: white;
            border-radius: 10px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
<div id="info_box_container">
    <span>Info Box</span><span style="float:right; font-size:small; cursor: pointer;" onclick="init_map()">reset</span><hr>
    <p>
        <label><input type="radio" name="level" class="level" value="0"/> Country</label>
        <label><input type="radio" name="level" class="level" value="1" checked/> Admin1</label>
        <label><input type="radio" name="level" class="level" value="2" /> Admin2</label>
    </p>
    <p>
        <input type="text" id="geoname-input" placeholder="'890098' grid_id" /><br>
        <button type="button" onclick="load_grid_id()">Get Geonames</button>
    </p>
    <p>
        <select id="load_states_by_code" class="country_list"></select>
        <br><button type="button" onclick="load_states_by_code()">Get States</button><button type="button" onclick="load_counties_by_code()">Get Counties</button>
    </p>
    <p>
        <select id="load_states_by_continent">
            <option value="6255146">Africa</option>
            <option value="6255147">Asia</option>
            <option value="6255148">Europe</option>
            <option value="6255149">North America</option>
            <option value="6255151">Oceania</option>
            <option value="6255150">South America</option>
            <option value="6255152">Antarctica</option>
        </select>
        <br><button type="button" onclick="load_states_by_continent()">Get States for Continent</button>
    </p>
    <hr>
    <div id="info_box"></div>
</div>

<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.min.js'></script>
<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.css' type='text/css' />
<div id='map'></div>

<script>
    // Initiate mapbox
    function init_map() {
        mapboxgl.accessToken = 'pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA';
        var map = new mapboxgl.Map({
            container: 'map', // container id
            style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
            center: [ 30.65833741577086, 27.32666933600865], // starting position [lng, lat]
            zoom: 6 // starting zoom
        });
        jQuery('#info_box').empty()
    }
    init_map()

    mapboxgl.accessToken = 'pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA';
    var map = new mapboxgl.Map({
        container: 'map', // container id
        style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
        center: [ 30.65833741577086, 27.32666933600865], // starting position [lng, lat]
        zoom: 6 // starting zoom
    });
    // load lists
    jQuery(document).ready(function(){
        jQuery.get('https://dt-mapping-builder/get-geonames-lists.php', { type: 'countries', value: 'all' }, null, 'json'  ).done( function(data) {
            if ( data !== undefined ) {
                let html = ''
                jQuery.each( data, function(i,v) {
                    html += '<option value="'+v.country_code+'">'+v.name+'</option>'
                })
                jQuery('.country_list').empty().append(html)
            }
        })

    })


    function load_grid_id() {
        let info_box = jQuery('#info_box')
        info_box.empty()
        let type = 'grid_id'
        let value = jQuery('#geoname-input').val()
        if ( value === undefined ) {
            return;
        }
        console.log(value)

        jQuery.get('https://dt-mapping-builder/get-geonames-lists.php', { type: type, value: value }, null, 'json' ).done(function(data) {
            console.log(data)
            if ( data !== undefined ) {
                // Info box
                jQuery.each(data, function(i,v) {
                    info_box.append( v.name + '<br>' )
                })

                let unique_source = '' + v.grid_id + Date.now()
                map.addSource(unique_source, {
                    type: 'geojson',
                    data: 'https://storage.googleapis.com/location-grid-mirror/low/' + v.grid_id + '.geojson'
                });
                map.addLayer({
                    "id": '' + v.grid_id + Date.now() + Math.random(),
                    "type": "fill",
                    "source": unique_source,
                    "paint": {
                        "fill-color": "#888888",
                        "fill-opacity": 0.4

                    },
                    "filter": ["==", "$type", "Polygon"]
                });
            }
        })
    }

    function load_states_by_code() {
        let info_box = jQuery('#info_box')
        info_box.empty()
        let type = 'states'
        let value = jQuery('#load_states_by_code').val()
        if ( value === undefined ) {
            return;
        }
        console.log(value)

        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: type, value: value }, null, 'json' ).done(function(data) {
            console.log(data)
            if ( data !== undefined ) {
                // Info box
                jQuery.each(data, function(i,v) {
                    info_box.append( v.name + '<br>' )

                    let unique_source = '' + v.grid_id + Date.now()
                    map.addSource(unique_source, {
                        type: 'geojson',
                        data: 'https://storage.googleapis.com/location-grid-mirror/low/' + v.grid_id + '.geojson'
                    });
                    map.addLayer({
                        "id": '' + v.grid_id + Date.now() + Math.random(),
                        "type": "fill",
                        "source": unique_source,
                        "paint": {
                            "fill-color": "#888888",
                            "fill-opacity": 0.4

                        },
                        "filter": ["==", "$type", "Polygon"]
                    });
                })
            }
        })
    }

    function load_counties_by_code() {
        let info_box = jQuery('#info_box')
        info_box.empty()
        let type = 'counties'
        let value = jQuery('#load_states_by_code').val()
        if ( value === undefined ) {
            return;
        }

        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: type, value: value }, null, 'json' ).done(function(data) {
            console.log(data)
            if ( data !== undefined ) {
                // Info box
                jQuery.each(data, function(i,v) {
                    info_box.append( v.name + '<br>' )

                    let unique_source = '' + v.grid_id + Date.now()
                    map.addSource(unique_source, {
                        type: 'geojson',
                        data: 'https://storage.googleapis.com/location-grid-mirror/low/' + v.grid_id + '.geojson'
                    });
                    map.addLayer({
                        "id": '' + v.grid_id + Date.now() + Math.random(),
                        "type": "fill",
                        "source": unique_source,
                        "paint": {
                            "fill-color": "#888888",
                            "fill-opacity": 0.4

                        },
                        "filter": ["==", "$type", "Polygon"]
                    });
                })
            }
        })
    }

    function load_states_by_continent() {
        let info_box = jQuery('#info_box')
        info_box.empty()
        let type = 'continent'
        let value = jQuery('#load_states_by_continent').val()
        if ( value === undefined ) {
            return;
        }

        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: type, value: value }, null, 'json' ).done(function(data) {
            console.log(data)
            if ( data !== undefined ) {
                // Info box
                jQuery.each(data, function(i,v) {
                    info_box.append( v.name + ' ('+v.grid_id+')<br>' )
                })

                jQuery.each(data, function(i,v) {
                    let unique_source = '' + v.grid_id + Date.now()
                    map.addSource(unique_source, {
                        type: 'geojson',
                        data: 'https://storage.googleapis.com/location-grid-mirror/low/' + v.grid_id + '.geojson'
                    });
                    map.addLayer({
                        "id": '' + v.grid_id + Date.now() + Math.random(),
                        "type": "fill",
                        "source": unique_source,
                        "paint": {
                            "fill-color": "#888888",
                            "fill-opacity": 0.4

                        },
                        "filter": ["==", "$type", "Polygon"]
                    });
                })

            }
        })
    }


    // Click Map Creator
    map.on('click', function (e) {
        let selected_level = jQuery('input[name=level]:checked').val()
        console.log(selected_level)

        let lng = e.lngLat.lng
        let lat = e.lngLat.lat

        // add marker
        new mapboxgl.Marker()
            .setLngLat(e.lngLat )
            .addTo(map);

        // info box
        infobox( lng, lat, selected_level )

        // add polygon
        jQuery.get('https://dt-mapping-builder/location-grid-geocoder.php', { type: 'geocode', longitude: lng, latitude:  lat, level: selected_level }, null, 'json' ).done(function(data) {
            // console.log(data)
            if ( data !== undefined ) {
                let unique_source = '' + data.grid_id + Date.now()
                map.addSource(unique_source, {
                    type: 'geojson',
                    data: 'https://storage.googleapis.com/location-grid-mirror/low/' + data.grid_id + '.geojson'
                });
                map.addLayer({
                    "id": '' + data.grid_id + Date.now() + Math.random(),
                    "type": "fill",
                    "source": unique_source,
                    "paint": {
                        "fill-color": "#888888",
                        "fill-opacity": 0.4
                    },
                    "filter": ["==", "$type", "Polygon"]
                });
            }
        })

    });

    // Search Box Tool
    let geoCode = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        types: 'country region place district postcode locality neighborhood address',
        marker: {
            color: 'orange'
        },
        mapboxgl: mapboxgl
    })
    map.addControl(geoCode); // add controller
    geoCode.on('result', function(e) { // respond to search
        console.log(e)
        let lat = e.result.center[1]
        let lng = e.result.center[0]

        // add info box
        infobox( lng, lat )

        // add polygon
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'grid_id', longitude: lng, latitude: lat }, null, 'json' ).done(function(data) {
            console.log(data)
            jQuery.each( data, function(i,v) {

                let unique_source = v + Date.now()
                map.addSource(unique_source, {
                    type: 'geojson',
                    data: 'https://storage.googleapis.com/location-grid-mirror/low/' + v + '.geojson'
                });
                map.addLayer({
                    "id": v + Date.now() + Math.random(),
                    "type": "fill",
                    "source": unique_source,
                    "paint": {
                        "fill-color": "#888888",
                        "fill-opacity": 0.4

                    },
                    "filter": ["==", "$type", "Polygon"]
                });
            })
        })

    })

    // User Geolocate Button
    let userGeocode = new mapboxgl.GeolocateControl({
        positionOptions: {
            enableHighAccuracy: true
        },
        marker: {
            color: 'orange'
        },
        trackUserLocation: false
    })
    map.addControl(userGeocode);
    userGeocode.on('geolocate', function(e) { // respond to search
        console.log(e)
        let lat = e.coords.latitude
        let lng = e.coords.longitude

        infobox( lng, lat )

        // add polygon
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'grid_id', longitude: lng, latitude:  lat }, null, 'json' ).done(function(data) {
            console.log(data)
            jQuery.each( data, function(i,v) {

                let unique_source = v + Date.now()
                map.addSource(unique_source, {
                    type: 'geojson',
                    data: 'https://storage.googleapis.com/location-grid-mirror/low/' + v + '.geojson'
                });
                map.addLayer({
                    "id": v + Date.now() + Math.random(),
                    "type": "fill",
                    "source": unique_source,
                    "paint": {
                        "fill-color": "#888888",
                        "fill-opacity": 0.4

                    },
                    "filter": ["==", "$type", "Polygon"]
                });
            })
        })
    })


</script>
</body>
</html>