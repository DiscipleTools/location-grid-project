<?php

if ( $_GET ) :

    /**
     * Set Variables
     */
    require_once( 'con.php' );
    $mapbox_key = 'pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA';
    $longitude = $_GET['longitude'];
    $latitude =  $_GET['latitude'];


    /**
     * Query Admin2 Level
     */
    $query = mysqli_query( $con, "
        SELECT g.name, c.name as country_name, a1.name as admin1_name, g.geonameid, g.population, g.level
        FROM dt_geonames as g
        LEFT JOIN dt_geonames as c ON g.country_geonameid=c.geonameid
        LEFT JOIN dt_geonames as a1 ON g.admin1_geonameid=a1.geonameid
        WHERE 
        g.north_latitude >= {$latitude} AND
        g.south_latitude <= {$latitude} AND
        g.west_longitude >= {$longitude} AND
        g.east_longitude <= {$longitude} AND
        g.level = 'admin2';
    " );
    $results = mysqli_fetch_all($query, MYSQLI_ASSOC);


    /**
     * Escalate Query to Admin1 Level, if Admin2 is missing
     */
    if ( empty( $results ) ) {
        $query = mysqli_query( $con, "
            SELECT g.name, c.name as country_name, g.geonameid, g.population, g.level
            FROM dt_geonames as g
            LEFT JOIN dt_geonames as c ON g.country_geonameid=c.geonameid
            WHERE 
            g.north_latitude >= {$latitude} AND
            g.south_latitude <= {$latitude} AND
            g.west_longitude >= {$longitude} AND
            g.east_longitude <= {$longitude} AND
            g.level = 'admin1';
        " );
        $results = mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    if ( empty( $results ) ) {
        echo 'No Country Data Found';
        return;
    }

    /**
     * Test 1: Test for exact match and return results.
     */

    if ( count( $results ) === 1  ) {
        content_response( $results[0], $longitude, $latitude );
    }

    /**
     * Test 2: Point in Polygon test to find exact match
     */
    if ( count( $results ) > 1  ) {

        foreach ( $results as $result ) {
            if ( _this_geonameid( $result['geonameid'], $longitude, $latitude ) ) {
                content_response( $result, $longitude, $latitude );
                break;
            }
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
 * Downloads GeoJSON polygons and parses through geometries trying to match lon/lat within the polygons
 *
 * @param $geonameid
 * @param $longitude_x
 * @param $latitude_y
 *
 * @return bool
 */
function _this_geonameid( $geonameid, $longitude_x, $latitude_y ) {

    // get geoname geojson
    $raw_geojson = file_get_contents( getcwd() . '/saturation-grid-project/polygon/'.$geonameid.'.geojson' );
    if ( ! $raw_geojson ) {
        return false;
    }
    $geojson = json_decode( $raw_geojson, true );
    $features = $geojson['features'];

    // handle Polygon and MultiPolygon geometries
    foreach ( $features as $feature ) {
        if ( $feature['geometry']['type'] === 'Polygon' ) {
            foreach ( $feature['geometry']['coordinates'] as $coordinates ) {

                $data = _split_polygon( $coordinates );

                $vertices_x = $data['longitude'];
                $vertices_y = $data['latitude'];
                $points_polygon = count( $vertices_x );  // number vertices - zero-based array

                if ( _is_in_polygon( $points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y ) ) {
                    return $geonameid;
                }
            }
        }
        else if ( $feature['geometry']['type'] === 'MultiPolygon' ) {
            foreach ( $feature['geometry']['coordinates'] as $top_coordinates ) {
                foreach ( $top_coordinates as $coordinates ) {

                    $data = _split_polygon( $coordinates );

                    $vertices_x = $data['longitude'];
                    $vertices_y = $data['latitude'];
                    $points_polygon = count( $vertices_x );  // number vertices - zero-based array

                    if ( _is_in_polygon( $points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y ) ) {
                        return $geonameid;
                    }

                }
            }
        }
    }
    return false;
}
/**
 * Takes a spilt list of lng/lats and compares with a single lng/lat to see if the single exists within the polygon
 *
 * @param $points_polygon
 * @param $vertices_x
 * @param $vertices_y
 * @param $longitude_x
 * @param $latitude_y
 *
 * @return bool|int
 */
function _is_in_polygon( $points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y ) {
    $i = $j = $c = 0;
    for ( $i = 0, $j = $points_polygon - 1; $i < $points_polygon; $j = $i ++ ) {
        if ( ( ( $vertices_y[ $i ] > $latitude_y != ( $vertices_y[ $j ] > $latitude_y ) ) && ( $longitude_x < ( $vertices_x[ $j ] - $vertices_x[ $i ] ) * ( $latitude_y - $vertices_y[ $i ] ) / ( $vertices_y[ $j ] - $vertices_y[ $i ] ) + $vertices_x[ $i ] ) ) ) {
            $c = ! $c;
        }
    }
    return $c;
}
/**
 * Takes the coordinates section of a geojson polygon and splits the lng/lat coordinates, so they can be used by _is_in_polygon
 *
 * @param array $polygon_geometry
 *
 * @return array
 */
function _split_polygon( array $polygon_geometry ) {
    $longitude = $latitude = $data = [];
    foreach ( $polygon_geometry as $vertices ) {
        $longitude[] = $vertices[0];
        $latitude[] = $vertices[1];
    }
    $data = [
        'longitude' => $longitude,
        'latitude' => $latitude
    ];
    return $data;
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
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'info', longitude: e.result.center[0], latitude:  e.result.center[1] }, null, 'html' ).done(function(data) {
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

                map.addLayer({
                    "id": v + Date.now() + i,
                    "type": "line",
                    "source": unique_source,
                    "paint": {
                        "line-color": "#fff",
                        "line-width": 2

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

        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'info', longitude: e.coords.longitude, latitude:  e.coords.latitude }, null, 'html' ).done(function(data) {
            jQuery('#info_box').empty().append( '<br>' + data )
        })

        // add polygon
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'geonameid', longitude: e.coords.longitude, latitude:  e.coords.latitude }, null, 'json' ).done(function(data) {
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

                map.addLayer({
                    "id": v + Date.now() + i,
                    "type": "line",
                    "source": unique_source,
                    "paint": {
                        "line-color": "#fff",
                        "line-width": 2

                    },
                    "filter": ["==", "$type", "Polygon"]
                });

            })
        })
    })


    // Click Map Creator
    map.on('click', function (e) {
        console.log(e)
        console.log(JSON.stringify(e.lngLat))

        // add marker
        new mapboxgl.Marker()
            .setLngLat(e.lngLat )
            .addTo(map);

        // info box
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'info', longitude: e.lngLat.lng, latitude:  e.lngLat.lat }, null, 'html' ).done(function(data) {
            jQuery('#info_box').empty().append( '<br>' + data )
        })

        // add polygon
        jQuery.get('https://dt-mapping-builder/<?php echo basename(__FILE__) ?>', { type: 'geonameid', longitude: e.lngLat.lng, latitude:  e.lngLat.lat }, null, 'json' ).done(function(data) {

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

                map.addLayer({
                    "id": v + Date.now() + i,
                    "type": "line",
                    "source": unique_source,
                    "paint": {
                        "line-color": "#fff",
                        "line-width": 2

                    },
                    "filter": ["==", "$type", "Polygon"]
                });

            })

        })
    });
</script>
</body>
</html>