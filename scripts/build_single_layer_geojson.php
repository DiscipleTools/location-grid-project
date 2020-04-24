<?php
require_once( 'con.php' );

print 'BEGIN';
include_once( getcwd() . '/vendor/phayes/geophp/geoPHP.inc'); // make sure to run $ composer install on the command line

/** FOLDERS */
$output = [
    'output' => getcwd() . '/output/',
    'gis' => getcwd() . '/output/gis/',
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
/* level name */
if ( isset( $argv[2] ) ) {
    $level_name = $argv[2];
} else {
    print 'level argument missing' . PHP_EOL;
    die();
}

if ( $code === 'all' ) {
    $query_raw = mysqli_query( $con,
        "SELECT DISTINCT admin0_code FROM {$tables['grid']}" );

    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $countries = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

} else {
    $countries = [ [ 'admin0_code' => $code ] ];
}

foreach ( $countries as $admin0_code ) {
    $print_admin0_code = $admin0_code['admin0_code'];

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
                FROM {$tables['grid']} as lg 
                JOIN {$tables['geometry']} as g ON g.grid_id=lg.grid_id 
                LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
                LEFT JOIN location_grid as a1 ON lg.admin1_grid_id=a1.grid_id
                LEFT JOIN location_grid as a2 ON lg.admin2_grid_id=a2.grid_id
                LEFT JOIN location_grid as a3 ON lg.admin3_grid_id=a3.grid_id
                LEFT JOIN location_grid as a4 ON lg.admin4_grid_id=a4.grid_id
                LEFT JOIN location_grid as a5 ON lg.admin5_grid_id=a5.grid_id
                WHERE lg.admin0_code = '{$admin0_code['admin0_code']}' AND lg.level_name = '{$level_name}'
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

        $features[] = array(
            "type" => "Feature",
            "properties" => array(
                "grid_id" => $result['grid_id'],
                "parent_id" => $result['parent_id'],
                "admin0_code" => $result['admin0_code'],
                "admin0_grid_id" => $result['admin0_grid_id'],
                "admin0_name" => $result['admin0_name'],
                "admin1_grid_id" => $result['admin1_grid_id'],
                "admin1_name" => $result['admin1_name'],
                "admin2_grid_id" => $result['admin2_grid_id'],
                "admin2_name" => $result['admin2_name'],
                "admin3_grid_id" => $result['admin3_grid_id'],
                "admin3_name" => $result['admin3_name'],
                "admin4_grid_id" => $result['admin4_grid_id'],
                "admin4_name" => $result['admin4_name'],
                "admin5_grid_id" => $result['admin5_grid_id'],
                "admin5_name" => $result['admin5_name'],
            ),
            "geometry" => json_decode( $geometry, true ),
        );
        print '#';
    }

    $geojson = array(
        'type' => "FeatureCollection",
        'features' => $features,
    );
    $geojson = json_encode( $geojson );
    $label = strtolower( $print_admin0_code . '_' . $level_name );

    file_put_contents( $output['gis'] . $label .  '.geojson', $geojson );

    print $label;

}

print 'END' . PHP_EOL;

mysqli_close($con );