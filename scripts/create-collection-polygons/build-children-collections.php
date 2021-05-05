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

if ( 1 == $code ) { // if building for the entire world.
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

//$list_raw = mysqli_query( $con,
//    "SELECT grid_id as parent_id FROM quality_check" );


if ( empty( $list_raw ) ) {
    print_r( $con );
    die();
}
$list = mysqli_fetch_all( $list_raw, MYSQLI_ASSOC );
$list = array_map(function ( $a ) { return $a['parent_id'];}, $list );

foreach( $list as $parent_id ) {

    $query_raw = mysqli_query( $con,
        "SELECT 
                lg.*, 
                g.geoJSON, 
                a0.name as admin0_name,
                a1.name as admin1_name,
                a2.name as admin2_name,
                a3.name as admin3_name,
                a4.name as admin4_name,
                a5.name as admin5_name
                FROM location_grid as lg 
                JOIN location_grid_geometry as g ON g.grid_id=lg.grid_id 
                LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
                LEFT JOIN location_grid as a1 ON lg.admin1_grid_id=a1.grid_id
                LEFT JOIN location_grid as a2 ON lg.admin2_grid_id=a2.grid_id
                LEFT JOIN location_grid as a3 ON lg.admin3_grid_id=a3.grid_id
                LEFT JOIN location_grid as a4 ON lg.admin4_grid_id=a4.grid_id
                LEFT JOIN location_grid as a5 ON lg.admin5_grid_id=a5.grid_id
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

    file_put_contents( $output['output'] . $parent_id .  '.geojson', $geojson );

}


print 'END' . PHP_EOL;