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

$query_raw = mysqli_query( $con,
    "SELECT lg.* FROM morocco_import lg WHERE (ADM2_EN = '' OR grid_id IS NOT NULL) AND admin1_grid_id IS NOT NULL ;" );

if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

/* Feature collection */
$features = [];
foreach( $query as $result ) {

    $geometry = $result['geoJSON'];

    $features[] = array(
        "type" => "Feature",
        "properties" => array(
            'grid_id' => $result['grid_id'],
            'name' => $result['name'],
            'admin0_grid_id' => $result['admin0_grid_id'],
            'admin1_grid_id' => $result['admin1_grid_id'],
            'admin2_grid_id' => $result['admin2_grid_id'],
            'ADM0_EN' => $result['ADM0_EN'],
            "ADM1_EN" => $result['ADM1_EN'],
            'ADM2_EN' => $result['ADM2_EN'],
        ),
        "geometry" => json_decode( $geometry, true ),
    );

}

$geojson = array(
    'type' => "FeatureCollection",
    'features' => $features,
);
$geojson = json_encode( $geojson );

file_put_contents( $output['output'] . 'source.geojson', $geojson );

print 'END' . PHP_EOL;