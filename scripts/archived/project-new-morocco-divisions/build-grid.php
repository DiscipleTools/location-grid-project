<?php
print 'Start' . PHP_EOL;

require_once( 'con.php' );

require_once( '../location-grid-geocoder-non-wp.php' );
$geocoder = new Location_Grid_Geocoder();
include_once( '../vendor/phayes/geophp/geoPHP.inc' );

if ( isset( $argv[1] ) ) {
    $code = $argv[1];
} else {
    print 'parent_id argument missing' . PHP_EOL;
    die();
}

$query  = mysqli_query( $con, "
            SELECT * FROM morocco WHERE level = '{$code}';" );
$results = mysqli_fetch_all( $query, MYSQLI_ASSOC );

$parent_level = $code - 1;
foreach( $results as $result ) {
    $query  = mysqli_query( $con, "
            SELECT * FROM morocco 
            WHERE 
                north_latitude >= {$result['latitude']} AND
                south_latitude <= {$result['latitude']} AND
                east_longitude >= {$result['longitude']} AND
                west_longitude <= {$result['longitude']} AND
                level = {$parent_level}

;" );
    if ( ! $query ) {
        continue;
    }
    $parent = mysqli_fetch_all( $query, MYSQLI_ASSOC );

    print 'For: '. $result['name'] . ' - ' . $result['grid_id'] .  PHP_EOL;

    if ( 1 == count($parent) ) {
        foreach( $parent as $child ) {
            print $child['name']  . ' - ' . $child['grid_id'] . PHP_EOL;
        }
    } else {

        foreach( $parent as $child ) {
            $features = [];
            $features[] = array(
                "type" => "Feature",
                "properties" => [],
                "geometry" => json_decode( $child['geoJSON'], true ),
            );
            $geojson = array(
                'type' => "FeatureCollection",
                'features' => $features,
            );
            $geojson = json_encode( $geojson );


            $features_point = [];
            $features_point[] = array(
                "type" => "Feature",
                "properties" => [],
                "geometry" => [
                    "type" => 'Point',
                    'coordinates' => [
                        (float) $result['longitude'],
                        (float) $result['latitude'],
                    ]
                ],
            );
            $point = array(
                'type' => "FeatureCollection",
                'features' => $features_point,
            );
            $point = json_encode( $point );

            $geometry = geoPHP::load( $geojson, 'geojson' );
            $point = geoPHP::load( $point, 'geojson');
//            print_r( $point->within($geometry) );
            $centroid = $geometry->contains($point);

                print $child['name']  . ' - ' . $child['grid_id'] . PHP_EOL;
//            if ($centroid) {
//            } else {
//                print 'false' . PHP_EOL;
//                print $centroid . PHP_EOL;
//            }

        }


    }

    print '-----------' . PHP_EOL;
}

mysqli_close($con);
print 'End' . PHP_EOL;