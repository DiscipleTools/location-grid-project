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
    SELECT grid_id
    FROM (
    SELECT  lg0.grid_id, a0.name, lg0.longitude, lg0.latitude
        FROM location_grid lg0
    LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
        WHERE lg0.level < 1
        AND lg0.country_code NOT IN (
            SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
        )
    AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
        AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    
        
        UNION ALL
        -- 
        # Only admin1
        -- 
        SELECT  lg1.grid_id, a0.name, lg1.longitude, lg1.latitude
        FROM location_grid as lg1 
    LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
        WHERE lg1.country_code NOT IN (
        SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
        ) AND lg1.level_name != 'admin0'
    AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
        AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
        
        UNION ALL
        -- 
        # Has admin2
        -- 
        SELECT  lg2.grid_id, a0.name, lg2.longitude, lg2.latitude
        FROM location_grid lg2 
    LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
        WHERE lg2.level_name = 'admin2'
    AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
        AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    
    
    UNION ALL
    
        SELECT  lg3.grid_id, a0.name, lg3.longitude, lg3.latitude
        FROM location_grid as lg3 
        LEFT JOIN location_grid as a0 ON lg3.admin0_grid_id=a0.grid_id
            WHERE lg3.level = 3
    AND a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
    
    UNION ALL
    
        SELECT  lg4.grid_id, a0.name, lg4.longitude, lg4.latitude
        FROM location_grid as lg4 
        LEFT JOIN location_grid as a0 ON lg4.admin0_grid_id=a0.grid_id
            WHERE lg4.level = 1
    AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    
    ) as tb
    ORDER BY longitude, name
" );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );
$list = array_map( function ( $a ) { return $a['grid_id'];}, $query );
$list = array_chunk( $list, 500 );

$file_name = 'world.geojson';

$geojson_start = '{"type":"FeatureCollection","features":[';
$geojson_end = ']}';

file_put_contents( $output['output'] . $file_name, $geojson_start );


foreach ( $list as $index => $chunk ) {

    $sql_prepared = dt_array_to_sql($chunk);

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
               WHERE lg.grid_id IN ({$sql_prepared})" )
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
        $geometry = json_decode( $geometry, true );
        if ( empty( $geometry ) ){
            $geometry = [];
        }

        $features[] = array(
            "type" => "Feature",
            "id" => $grid_id,
            "properties" => array(
                'full_name' => _full_name( $result ),
                "grid_id" => $result['grid_id'],
            ),
            "geometry" => $geometry,
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

    print 'Chunk ' . $index . PHP_EOL;

}

file_put_contents( $output['output'] . $file_name, $geojson_end, FILE_APPEND );

print 'END' . PHP_EOL;

mysqli_close($con );

function dt_array_to_sql( $values) {
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