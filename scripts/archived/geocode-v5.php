<?php

if ( $_GET ) :

    require_once( 'con.php' );
    $longitude = $_GET['longitude'];
    $latitude =  $_GET['latitude'];

    require_once( 'location-grid-geocoder.php' );
    $geocoder = new Location_Grid_Geocoder();

    $response_array =  $geocoder->get_geonameid_by_lnglat( $longitude, $latitude );
    if ( $response_array ) {
        content_response( $response_array, $longitude, $latitude );
    }

    return; // return to block the page from serving up the HTML at the bottom.

endif; // html

/**
 * Responds to URL request with requested content
 *
 * @param $response_array
 * @param $longitude
 * @param $latitude
 */
function content_response( $response_array, $longitude, $latitude ) {

    switch ( $response_array['level'] ) {
        case 'admin2':

            // info
            if ( $_GET['type'] === 'info' ){
                echo 'Longitude: ' .$longitude. '<br>Latitude: ' .$latitude. '<br>';
                echo '<br><hr><br>';
                echo  $response_array['country_name'] . ' <br>  |--- ' . $response_array['admin1_name'] . ' <br>  |---  |---  ' . $response_array['name'] . ' (' . $response_array['geonameid'] . ')' . '<br>';
                echo '<br><hr><br><strong>'.$response_array[ 'name' ] .' Info:</strong><br>&nbsp;&nbsp;Population: '.$response_array['population'].'<br>&nbsp;&nbsp;Contacts: 0<br>&nbsp;&nbsp;Groups: 0<br>&nbsp;&nbsp;Churches: 0<br>&nbsp;&nbsp;Trainings: 0<br>&nbsp;&nbsp;Workers: 0<br>';
            }
            // geonameid
            if ( $_GET['type'] === 'geonameid' ){
                echo json_encode( [ $response_array['geonameid'] ] );
            }

            break;
        case 'admin1':

            // info
            if ( $_GET['type'] === 'info' ){
                echo 'Longitude: ' .$longitude. '<br>Latitude: ' .$latitude. '<br>';
                echo '<br><hr><br>';
                echo  $response_array['country_name'] . ' <br>  |--- ' . $response_array['name'] . ' (' . $response_array['geonameid'] . ')' . '<br>';
                echo '<br><hr><br><strong>'.$response_array[ 'name' ] .' Info:</strong><br>&nbsp;&nbsp;Population: '.$response_array['population'].'<br>&nbsp;&nbsp;Contacts: 0<br>&nbsp;&nbsp;Groups: 0<br>&nbsp;&nbsp;Churches: 0<br>&nbsp;&nbsp;Trainings: 0<br>&nbsp;&nbsp;Workers: 0<br>';
            }
            // geonameid
            if ( $_GET['type'] === 'geonameid' ){
                echo json_encode( [ $response_array['geonameid'] ] );
            }

            break;
    }
}


/**
 * Below is the mapbox HTML page
 * The mapbox forms self reference this page.
 */
?>
<html>
<head>
    <meta charset='utf-8' />
    <title>Sample Geocode</title>
    <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.54.0/mapbox-gl.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.54.0/mapbox-gl.css' rel='stylesheet' />
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
    <span>Info Box</span><hr>
    <div id="info_box"></div>
</div>

<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.min.js'></script>
<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.css' type='text/css' />
<div id='map'></div>

<script>
    mapboxgl.accessToken = 'pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA';
    var map = new mapboxgl.Map({
        container: 'map', // container id
        style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
        center: [-104.98863499839904, 39.49382417503958], // starting position [lng, lat]
        zoom: 6 // starting zoom
    });

    // Search
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

        // add info box
        jQuery.get('https://dt-mapping-builder/geocode-info-box.php', { type: 'info', longitude: e.result.center[0], latitude:  e.result.center[1] }, null, 'html' ).done(function(data) {
            jQuery('#info_box').empty().append( data )
        })

        // add polygon
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'geonameid', longitude: e.result.center[0], latitude: e.result.center[1] }, null, 'json' ).done(function(data) {
            console.log(data)
            jQuery.each( data, function(i,v) {

                let unique_source = v + Date.now()
                map.addSource(unique_source, {
                    type: 'geojson',
                    data: 'https://dt-mapping-builder/saturation-grid-project/polygon/' + v + '.geojson'
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

        jQuery.get('https://dt-mapping-builder/geocode-info-box.php', { type: 'info', longitude: lng, latitude: lat  }, null, 'html' ).done(function(data) {
            jQuery('#info_box').empty().append( '<br>' + data )
        })

        // add polygon
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'geonameid', longitude: lng, latitude:  lat }, null, 'json' ).done(function(data) {
            console.log(data)
            jQuery.each( data, function(i,v) {

                let unique_source = v + Date.now()
                map.addSource(unique_source, {
                    type: 'geojson',
                    data: 'https://dt-mapping-builder/saturation-grid-project/polygon/' + v + '.geojson'
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