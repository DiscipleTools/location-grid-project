<?php
/**
 * This script generates a geojson file for the world including all units down to admin2.
 * All admin2 geometries,
 * All admin1 geometries for places where there is no admin2
 * All admin0 geometries for places where there is no admin1
 *
 * Run from command line: $ php build-geojson-for-world-toAdmin2.php
 */
require_once( 'con.php' );

print 'BEGIN' . PHP_EOL;
include_once( '../vendor/phayes/geophp/geoPHP.inc'); // make sure to run $ composer install on the command line

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
    "
    SELECT DISTINCT admin0_grid_id as grid_id, max(level) as level FROM location_grid WHERE admin0_grid_id IS NOT NULL GROUP BY admin0_grid_id;
    " );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

$file_name = 'outlines.geojson';

$geojson_start = '{"type":"FeatureCollection","features":[';
$geojson_end = ']}';

file_put_contents( $output['output'] . $file_name, $geojson_start );


foreach ( $query as $index => $row ) {
    $grid_id = $row['grid_id'];

    $query_raw = mysqli_query( $con,
        "
        SELECT
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
               WHERE lg.grid_id = {$grid_id}" )
    ;
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
                "level_max" => $row['level'], // max levels available for this country
                'country_code' => $result['country_code'],
                'full_name' => _full_name( $result ),
                'n' => $result['north_latitude'],
                's' => $result['south_latitude'],
                'e' => $result['east_longitude'],
                'w' => $result['west_longitude']
            ),
            "geometry" => json_decode( $geometry, true ),
        );

        print '#';
    }
    $features = json_encode( $features );
    $features = ltrim( $features, '[');
    $features = rtrim( $features, ']');

    if ( $index !== 0 ) {
        $features = ',' . $features;
    }

    file_put_contents( $output['output'] . $file_name, $features, FILE_APPEND );

}

file_put_contents( $output['output'] . $file_name, $geojson_end, FILE_APPEND );

print 'END' . PHP_EOL;

mysqli_close($con );

function _dt_array_to_sql( $values) {
    if (empty( $values )) {
        return 'NULL';
    }
    foreach ($values as &$val) {
        if ('\N' === $val) {
            $val = 'NULL';
        } else {
            $val = "'" . trim( $val ) . "'";
        }
    }
    return implode( ',', $values );
}

function _full_name( $row ) {
    $label = '';

    if ( ! empty( $row['admin0_name'] ) ) {
        $label = $row['admin0_name'];
    }
    if ( ! empty( $row['admin1_name'] ) ) {
        $label = $row['admin1_name']  . ', ' . $row['admin0_name'];
    }
    if ( ! empty( $row['admin2_name'] ) ) {
        $label = $row['admin2_name'] . ', ' . $row['admin1_name']  . ', ' . $row['admin0_name'];
    }

    return $label;
}