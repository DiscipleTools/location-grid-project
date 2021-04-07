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

/* parent id */
if ( isset( $argv[1] ) ) {
    $code = $argv[1];
} else {
    print 'parent_id argument missing' . PHP_EOL;
    die();
}
if ( isset( $argv[2] ) ) {
    $level = $argv[2];
} else {
    print 'level argument missing' . PHP_EOL;
    die();
}



$query_raw = mysqli_query( $con,
    "SELECT lg.*, lgg.geoJSON
            FROM location_grid lg
            LEFT JOIN location_grid_geometry lgg ON lgg.grid_id=lg.grid_id
            WHERE lg.admin0_grid_id = {$code} AND level_name = '{$level}'" );


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

file_put_contents( $output['output'] . $code . '_' . $level .  '.geojson', $geojson );

print 'END' . PHP_EOL;