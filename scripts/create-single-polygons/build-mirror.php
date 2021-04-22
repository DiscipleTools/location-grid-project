<?php
/**
 * Create all files in a country.
 * $ php {filename} {grid_id} // defaults to admin0 query
 * $ php {filename} {grid_id} {true} // queries by parent_id
 */
require_once( 'con.php' );
print 'BEGIN' . PHP_EOL;

/** FOLDERS */

// override destination output
if ( isset( $argv[1] ) ) {
    $output = [
        'output' => $argv[1],
    ];
}

foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

$list_raw = mysqli_query( $con,
    "SELECT lg.grid_id
                    FROM location_grid as lg;" );

if ( empty( $list_raw ) ) {
    print_r( $con );
    die();
}
$list = mysqli_fetch_all( $list_raw, MYSQLI_ASSOC );
$list = array_map(function ( $a ) { return $a['grid_id'];}, $list );

foreach( $list as $id ) {

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
                    LEFT JOIN location_grid_geometry as g ON g.grid_id=lg.grid_id 
                    LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
                    LEFT JOIN location_grid as a1 ON lg.admin1_grid_id=a1.grid_id
                    LEFT JOIN location_grid as a2 ON lg.admin2_grid_id=a2.grid_id
                    LEFT JOIN location_grid as a3 ON lg.admin3_grid_id=a3.grid_id
                    LEFT JOIN location_grid as a4 ON lg.admin4_grid_id=a4.grid_id
                    LEFT JOIN location_grid as a5 ON lg.admin5_grid_id=a5.grid_id
                    WHERE lg.grid_id = '{$id}'
                    " );

    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $result = mysqli_fetch_assoc( $query_raw );

    /* Feature collection */
    $features = [];
    $geometry = $result['geoJSON'];
    if ( empty( $geometry ) ) {
        continue;
    }

    $features[] = array(
        "type" => "Feature",
        'id' => $result['grid_id'],
        "properties" => array(
            "grid_id" => (int) $result['grid_id'],
            'full_name' => _full_name($result),
        ),
        "geometry" => json_decode( $geometry, true ),
    );
    $geojson = array(
        'type' => "FeatureCollection",
        'features' => $features,
    );
    $geojson = json_encode( $geojson );
    $geojson = trim(preg_replace('/\n/', '', $geojson));
    $geojson = trim(preg_replace('/, "/', ',"', $geojson));
    $geojson = trim(preg_replace('/: "/', ':"', $geojson));
    $geojson = trim(preg_replace('/: \[/', ':[', $geojson));
    $geojson = trim(preg_replace('/: {/', ':{', $geojson));

    file_put_contents( $output['output'] . $result['grid_id'] .  '.geojson', $geojson );

    print '#';
}

print PHP_EOL . 'END' . PHP_EOL;