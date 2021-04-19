<?php
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
    print 'admin0_code argument missing' . PHP_EOL;
    die();
}

$query_raw = mysqli_query( $con,
    "SELECT 
                lg.*, 
                lg.geoJSON, 
                a0.name as admin0_name,
                a1.name as admin1_name,
                a2.name as admin2_name,
                a3.name as admin3_name,
                a4.name as admin4_name,
                a5.name as admin5_name
                FROM morocco as lg 
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

    $geometry = geoPHP::load( $geojson, 'geojson' );
    $centroid = $geometry->getCentroid();
    print_r($centroid);

    $bounds = $geometry->getBBox();
    print_r($bounds);

    file_put_contents( $output['output'] . $grid_id .  '.geojson', $geojson );

}

print 'END' . PHP_EOL;