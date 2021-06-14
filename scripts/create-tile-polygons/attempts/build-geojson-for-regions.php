<?php
/**
 * Creates geojson regions
 *
 *  Northern Africa	015
    Sub-Saharan Africa	202
    Latin America and the Caribbean	419
    Northern America	021
    Central Asia	143
    Eastern Asia	030
    South-eastern Asia	035
    Southern Asia	034
    Western Asia	145
    Eastern Europe	151
    Northern Europe	154
    Southern Europe	039
    Western Europe	155
    Australia and New Zealand	053
    Melanesia	054
    Micronesia	057
    Polynesia	061
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

if ( isset( $argv[1] ) ) {
    $code = $argv[1];
} else {
    print 'parent_id argument missing' . PHP_EOL;
    die();
}
$level = '';
if ( isset( $argv[2] ) ) {
    $level = $argv[2];
}

if ( $level === 'inter') {
    $region = mysqli_query( $con,
        "SELECT grid_id 
                FROM location_grid_m49
                WHERE m49_intermediate_region_code = {$code};" );
} else {
    $region = mysqli_query( $con,
        "SELECT grid_id 
                FROM location_grid_m49
                WHERE m49_subregion_code = {$code};" );
}



$region = mysqli_fetch_all( $region, MYSQLI_ASSOC );
$region = array_map(function ( $a ) { return $a['grid_id'];}, $region );
$country_list = implode(',', $region );

//print $regions_sql;
//die();

$query_raw = mysqli_query( $con,
    "

SELECT 
-- SUM(tbl.count)
*
FROM (

    -- 
    # Only with admin0
    -- 
    SELECT  
	a0.grid_id, a0.name, lg0.level_name, count(lg0.grid_id) as count
    FROM location_grid lg0
    LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
    WHERE lg0.level < 1
    AND lg0.country_code NOT IN (
        SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
    )
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    GROUP BY  a0.grid_id, a0.name, lg0.level_name
    
    UNION ALL
    -- 
    # Only admin1
    -- 
    SELECT 
    a0.grid_id, a0.name, lg1.level_name, count(lg1.grid_id) as count
    FROM location_grid as lg1 
    LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
    WHERE lg1.country_code NOT IN (
    SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
    ) AND lg1.level_name != 'admin0'
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    GROUP BY a0.grid_id, a0.name, lg1.level_name
    
    UNION ALL
    -- 
    # Has admin2
    -- 
    SELECT 
	a0.grid_id, a0.name, lg2.level_name, count(lg2.grid_id)  as count
    FROM location_grid lg2 
    LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
    WHERE lg2.level_name = 'admin2' 
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    GROUP BY a0.grid_id, a0.name, lg2.level_name


    UNION ALL
	# Exceptions admin3

    SELECT 
    a0.grid_id, a0.name, lge.level_name, count(lge.grid_id)  as count
    FROM location_grid lge 
    LEFT JOIN location_grid as a0 ON lge.admin0_grid_id=a0.grid_id
    WHERE a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
        AND lge.level_name = 'admin3' 
    GROUP BY a0.grid_id, a0.name, lge.level_name
    
    
    UNION ALL
    
    # Exceptions admin1
    
    SELECT 
    a0.grid_id, a0.name, lge1.level_name, count(lge1.grid_id) as count
    FROM location_grid lge1 
    LEFT JOIN location_grid as a0 ON lge1.admin0_grid_id=a0.grid_id
    WHERE lge1.level_name = 'admin1' 
    AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    GROUP BY a0.grid_id, a0.name, lge1.level_name

) as tbl
WHERE grid_id IN ($country_list)
ORDER BY name;
;
    " );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

$file_name = $code . '.geojson';

$geojson_start = '{"type":"FeatureCollection","features":[';
$geojson_end = ']}';

file_put_contents( $output['output'] . $file_name, $geojson_start );


foreach ( $query as $index => $country ) {
    $grid_id = $country['grid_id'];
    $level_name = $country['level_name'];

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
                WHERE lg.admin0_grid_id = '{$grid_id}' AND lg.level_name = '{$level_name}'
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
                'full_name' => _full_name( $result ),
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