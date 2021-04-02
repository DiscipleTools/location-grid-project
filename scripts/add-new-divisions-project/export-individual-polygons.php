<?php
ini_set('memory_limit', '50000M');
/**
 * This script gets the exported geojson and generates individual geojson files for each polygon.
 */

print 'Start' . PHP_EOL;
$dir = 'output/';

$geojson = file_get_contents($dir . 'geometry-with-name.geojson');
$geojson = json_decode( $geojson, true );

$i = 1;
foreach( $geojson['features'] as $feature ) {
    print '*';
    $features = [];

    $features[] = $feature;
    $geojson = array(
        'type' => "FeatureCollection",
        'features' => $features,
    );
    $geojson = json_encode( $geojson );

    file_put_contents( $dir . $feature['properties']['id'] . '.geojson', $geojson );
    $i++;
}

print 'End' . PHP_EOL;
