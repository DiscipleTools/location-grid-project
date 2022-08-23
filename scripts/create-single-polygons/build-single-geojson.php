<?php
require_once( 'con.php' );

print 'BEGIN' . PHP_EOL;
//include_once( '../vendor/phayes/geophp/geoPHP.inc' ); // make sure to run $ composer install on the command line

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
    print 'admin0_code argument missing' . PHP_EOL;
    die();
}

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
                WHERE lg.grid_id = '{$code}'
                " );

if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );


foreach( $query as $item ){
    /* Feature collection */
    $features = [];
    $grid_id = $item['grid_id'];
    $geometry = $item['geoJSON'];

    $features[] = array(
        "type" => "Feature",
        'id' => $item['grid_id'],
        "properties" => array(
            'full_name' => _full_name( $item ),
            "grid_id" => $item['grid_id'],
        ),
        "geometry" => json_decode( $geometry, true ),
    );
    print $item['grid_id'] . PHP_EOL;

    $geojson = array(
        'type' => "FeatureCollection",
        'features' => $features,
    );

//    print_r ( $geojson );

    $geojson = json_encode( $geojson );
    $geojson = trim(preg_replace('/\n/', '', $geojson));

    print $output['output'] .  $item['grid_id'] .  '.geojson';
    file_put_contents( $output['output'] .  $item['grid_id'] .  '.geojson', $geojson );

}

print 'END' . PHP_EOL;