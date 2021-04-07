<?php
/**
 * Get the geojson file and find the center lnglat and save it to the grid_id record.
 *
 * php update-lnglat-from-polygon.php 100386740
 */
print 'Start' . PHP_EOL;

require_once( '../con.php' );
include_once( '../vendor/phayes/geophp/geoPHP.inc');


if (! isset( $argv[1] ) ){
    print 'Must have grid_id as an option. End' . PHP_EOL;
}

$dir = 'output/';
//$file = $argv[1];
$grid_id = $argv[1];



$geojson = file_get_contents( $dir . $grid_id . '.geojson' );
if (! $geojson ){
    print 'geojson not found' . PHP_EOL;
}

$value = geoPHP::load();


$geojson = json_decode( $geojson, true );

$query  = mysqli_query( $con, "
            SELECT * FROM location_grid WHERE grid_id = {$grid_id}
            " );
$results = mysqli_fetch_all( $query, MYSQLI_ASSOC );

print_r($results);

// get the center for polygon

// add center to database




mysqli_close($con);
print 'End' . PHP_EOL;
