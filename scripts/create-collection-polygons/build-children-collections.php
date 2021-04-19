<?php
/**
 * $ php {filename} {grid_id} // defaults to admin0 query
 * $ php {filename} {grid_id} {true} // queries by parent_id
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
    $layer = 'parent_id';
} else {
    $layer = 'admin0_grid_id';
}

if ( 1 === $code ) { // if building for the entire world.
    $list_raw = mysqli_query( $con,
        "SELECT DISTINCT lg.parent_id
                FROM location_grid lg
                WHERE lg.parent_id IS NOT NULL" );
} else {
    $list_raw = mysqli_query( $con,
        "SELECT DISTINCT lg.parent_id
            FROM location_grid lg
            WHERE lg.{$layer} = {$code} AND lg.parent_id != 1" );
}


if ( empty( $list_raw ) ) {
    print_r( $con );
    die();
}
$list = mysqli_fetch_all( $list_raw, MYSQLI_ASSOC );
$list = array_map(function ( $a ) { return $a['parent_id'];}, $list );

foreach( $list as $parent_id ) {

    $query_raw = mysqli_query( $con,
        "SELECT lg.*, lgg.geoJSON
            FROM location_grid lg
            LEFT JOIN location_grid_geometry lgg ON lgg.grid_id=lg.grid_id
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
            'id' => $result['grid_id'],
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
    $geojson = trim(preg_replace('/\n/', '', $geojson));

    file_put_contents( $output['output'] . $parent_id .  '.geojson', $geojson );

}


print 'END' . PHP_EOL;