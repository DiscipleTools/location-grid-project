<?php
/**
 * This is a GIS full admin layer.
 *
 * $ php {{filename}}.php {{parent_id}} {{level_name}}
 */
require_once( 'con.php' );

print 'BEGIN' . PHP_EOL;
include_once( '../vendor/phayes/geophp/geoPHP.inc' ); // make sure to run $ composer install on the command line

/** FOLDERS */
$output = [
    'output' => getcwd() . '/output/',
    'output/0' => getcwd() . '/output/0/',
    'output/1' => getcwd() . '/output/1/',
    'output/2' => getcwd() . '/output/2/',
    'output/3' => getcwd() . '/output/3/',
    'output/4' => getcwd() . '/output/4/',
    'output/5' => getcwd() . '/output/5/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

/* parent id */
if ( isset( $argv[1] ) ) {
    $level = $argv[1];
} else {
    print 'parent_id argument missing' . PHP_EOL;
    die();
}

$list_raw = mysqli_query( $con,
    "SELECT DISTINCT lg.admin0_grid_id as grid_id
            FROM location_grid lg
            WHERE level = {$level}" );
if ( empty( $list_raw ) ) {
    print_r( $con );
    die();
}
$list = mysqli_fetch_all( $list_raw, MYSQLI_ASSOC );
$list = array_map(function ( $a ) { return $a['grid_id'];}, $list );

foreach ( $list as $code ){
    $query_raw = mysqli_query( $con,
        "SELECT lg.*, lgg.geoJSON
            FROM location_grid lg
            LEFT JOIN location_grid_geometry lgg ON lgg.grid_id=lg.grid_id
            WHERE lg.admin0_grid_id = {$code} AND level = {$level}" );
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
            'id' => $result['grid_id'],
            "properties" => array(
                'full_name' => _full_name($result),
                "grid_id" => $result['grid_id'],
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
    $geojson = trim(preg_replace('/\n/', '', $geojson));

    file_put_contents( $output['output'] . $level . '/' . $code .  '.geojson', $geojson );
}


print 'END' . PHP_EOL;