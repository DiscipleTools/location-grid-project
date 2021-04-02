<?php
ini_set('memory_limit', '50000M');
/**
 * Parses geojson and pulls out the polygon geometry provided by the shape file
 * then it builds a new geojson as a feature collection and adds ids to each geometry
 * then it creates a file in the output folder
 *
 * Run this script from the command line inside the folder $ php add-id-to-geojson.php
 */
print 'Start' . PHP_EOL;

$geojson = file_get_contents('mex_admin2.geojson');
$geojson = json_decode( $geojson, true );

$dir = 'output/';

$i = 1;

$features = [];
foreach( $geojson['geometries'] as $geometry ) {
    print '*';

    $features[] = array(
        "type" => "Feature",
        "properties" => array(
            "id" => $i,
        ),
        "geometry" => $geometry,
    );

    $i++;
}

$geojson = array(
    'type' => "FeatureCollection",
    'features' => $features,
);
$geojson = json_encode( $geojson );

file_put_contents( $dir . 'geometry-with-name.geojson', $geojson );

print 'End' . PHP_EOL;
