<?php
/**
 * $ php build-single-collection.php {{parent_id}}
 */
require_once( 'con.php' );

print 'BEGIN' . PHP_EOL;
include_once( '../vendor/phayes/geophp/geoPHP.inc' ); // make sure to run $ composer install on the command line

/** FOLDERS */
$output = [
    'output' => getcwd() . '/output/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

/* parent id */
if ( isset( $argv[1] ) ) {
    $code = $argv[1];
} else {
    print 'parent_id argument missing' . PHP_EOL;
    die();
}

$query_raw = mysqli_query( $con,
    "SELECT lg.*, lg.geoJSON
            FROM morocco lg
            WHERE lg.parent_id = {$code}" );

if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

/* Feature collection */
$features = [];
foreach( $query as $result ) {

    $grid_id = $result['grid_id'];
    $geometry = $result['geoJSON'];

    $features[] = array(
        "type" => "Feature",
        "properties" => array(
            'name' => $result['name'],
            'id' => $result['grid_id'],
            "grid_id" => $result['grid_id'],
            'country_code' => $result['country_code'],
            "admin0_code" => $result['admin0_code'],
            "level" => $result['level_name'],
            "parent_id" => $result['parent_id'],
            'center_lat' => $result['latitude'],
            'center_lng' => $result['longitude'],
            'FID' => $result['grid_id'],
        ),
        "geometry" => json_decode( $geometry, true ),
    );
    print $result['grid_id'] . PHP_EOL;
}

$geojson = array(
    'type' => "FeatureCollection",
    'features' => $features,
);
$geojson = json_encode( $geojson );

file_put_contents( $output['output'] . $code .  '.geojson', $geojson );

print 'END' . PHP_EOL;