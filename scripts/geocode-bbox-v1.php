<?php

if ( $_GET ) :

    require_once( 'geocode-bbox.php' );
    $mapbox_key = 'pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA';
    $longitude = $_GET['longitude'];
    $latitude =  $_GET['latitude'];

    require_once( 'location-grid-geocoder.php' );
    $geocoder = new Location_Grid_Geocoder();

    $record =  $geocoder->get_geonameid_by_lnglat( $longitude, $latitude );

    $query = mysqli_query( $con, "
            SELECT g.*, c.name as country_name, a1.name as admin1_name, a2.name as admin2_name
            FROM {$tables['geonames']} as g
            LEFT JOIN {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
            LEFT JOIN {$tables['geonames']} as a1 ON g.admin1_geonameid=a1.geonameid
            LEFT JOIN {$tables['geonames']} as a2 ON g.admin2_geonameid=a2.geonameid
            WHERE g.geonameid = {$record['geonameid']};
        " );
    if ( $query ) {

        if ( $_GET['type'] === 'info' || $_GET['type'] === 'geonameid' ) {
            $result = mysqli_fetch_assoc( $query );
            content_response( $result, $longitude, $latitude );
            return;
        }
        if ( $_GET['type'] === 'bbox' ) {
            header('Content-type: application/json');
            $item = mysqli_fetch_assoc( $query );

            $html = '{"type":"FeatureCollection","features":[';
            $html .= '{"type": "Feature","geometry": ';
            $html .= '{"type":"Polygon","coordinates":[[['.$item['west_longitude'].','.$item['north_latitude'].'],['.$item['east_longitude'].','.$item['north_latitude'].'],['.$item['east_longitude'].','.$item['south_latitude'].'],['.$item['west_longitude'].','.$item['south_latitude'].'],['.$item['west_longitude'].','.$item['north_latitude'].']]]}';
            $html .= ',"properties":{';
            $html .= '"name":"' . $item[ 'name' ] . '",';
            $html .= '"id":"' . $item[ 'geonameid' ] . '",';
            $html .= '"country_code":"' . $item[ 'country_code' ] . '",';
            $html .= '"admin1_code":"' . $item[ 'admin1_code' ] . '",';
            $html .= '"admin2_code":"' . $item[ 'admin2_code' ] . '",';
            $html .= '"center_lat":' . (float) $item[ 'latitude' ] . ',';
            $html .= '"center_lng":' . (float) $item[ 'longitude' ] . ',';
            $html .= '"geonameid":' . $item[ 'geonameid' ];
            $html .= '}';
            $html .= ',"id":"' . $item[ 'geonameid' ] . '"';

            $html .= '}';
            $html .= ']}';

            print $html;
            return;
        }

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
    <title>Sample Bounding Box</title>
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


    //
    // Click Map Creator
    //
    map.on('click', function (e) {
        // add marker
        new mapboxgl.Marker()
            .setLngLat(e.lngLat )
            .addTo(map);

        // info box
        let lat = e.lngLat.lat
        let lng = e.lngLat.lng

        jQuery.get('https://dt-mapping-builder/geocode-info-box.php', { type: 'info', longitude: lng, latitude:  lat }, null, 'html' ).done(function(data) {
            jQuery('#info_box').empty().append( '<br>' + data )
        })
        // polygon
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'bbox', longitude: lng, latitude:  lat }, null, 'json' ).done(function(data) {
           console.log(data)
            let unique_source =  '' + Date.now() + Math.random()
            let unique_id = Date.now() + Math.random() + 1
            map.addSource(unique_source, {
                type: 'geojson',
                data: data
            });
            map.addLayer({
                "id": unique_id,
                "type": "line",
                "source": unique_source,
                "paint": {
                    "line-color": "red",
                    "line-opacity": 0.8

                },
                "filter": ["==", "$type", "Polygon"]
            });
        })
        // add polygon
        jQuery.get('https://dt-mapping-builder/location-grid-geocoder.php', { type: 'geocode', longitude: lng, latitude:  lat }, null, 'json' ).done(function(data) {
            console.log(data)
            jQuery.each( data, function(i,v) {

                let unique_source = '' + v.geonameid + Date.now()
                map.addSource(unique_source, {
                    type: 'geojson',
                    data: 'https://dt-mapping-builder/saturation-grid-project/polygon/' +  v.geonameid + '.geojson'
                });
                map.addLayer({
                    "id": '' + v.geonameid + Date.now() + Math.random(),
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
    });
</script>
</body>
</html>