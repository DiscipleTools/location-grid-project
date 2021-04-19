<?php
/**
 * $ php {{filename}}.php {{parent_id}} {{level_name}}
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


$query_raw = mysqli_query( $con,
    "SELECT lg.*
            FROM morocco_import lg
            WHERE lg.ADM2_PCODE != ''
            " );


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

    $props = $result;
    unset($props['geoJSON']);


    $features[] = array(
        "type" => "Feature",
        "properties" => $props,
        "geometry" => json_decode( $geometry, true ),
    );
    print $result['grid_id'] . PHP_EOL;
}

$geojson = array(
    'type' => "FeatureCollection",
    'features' => $features,
);
$geojson = json_encode( $geojson );

file_put_contents( $output['output'] .'morocco_import.geojson', $geojson );

print 'END' . PHP_EOL;