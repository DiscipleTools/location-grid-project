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
if ( isset( $argv[2] ) ) {
    $level = $argv[2];
} else {
    print 'level argument missing' . PHP_EOL;
    die();
}

$lg = 'location_grid';
$lgg = 'location_grid_geometry';

$list_raw = mysqli_query( $con,
    "SELECT lg.*
            FROM {$lg} lg
            WHERE lg.country_code = '{$code}' AND level = {$level}" );

if ( empty( $list_raw ) ) {
    print_r( $con );
    die();
}

$list = mysqli_fetch_all( $list_raw, MYSQLI_ASSOC );
$list = array_map(function ( $a ) { return $a['grid_id'];}, $list );

foreach ( $list as $parent_id ){
    $query_raw = mysqli_query( $con,
        "SELECT lg.*, lgg.geoJSON
            FROM {$lg} lg
            JOIN {$lgg} as lgg ON lg.grid_id=lgg.grid_id
            WHERE lg.parent_id = {$parent_id}" );

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

    file_put_contents( $output['output'] . $parent_id .  '.geojson', $geojson );
}

print 'END' . PHP_EOL;