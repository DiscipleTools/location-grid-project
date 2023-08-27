<?php
// Builds a balanced states view of the world.
include('con.php');

print 'BEGIN' . PHP_EOL;
$output = getcwd() . '/output/';
if( ! is_dir( $output ) ) {
    mkdir( $output, 0755, true);
}

$query_raw = mysqli_query( $con,
    "
        SELECT * 
        FROM location_grid lg
        JOIN location_grid_groupings lgg ON lgg.grid_id=lg.grid_id AND lgg.grouping = 'zume_base'
        ORDER BY longitude, name;
            " );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

print 'Total Count' . PHP_EOL;
print count($query) . PHP_EOL;

$list = array_map( function ( $a ) { return $a['grid_id'];}, $query );
$list = array_chunk( $list, 500 );

$file_name = 'world.geojson';
if ( ! empty( $argv[1] ) ) {
    $file_name = $argv[1];
}

$geojson_start = '{"type":"FeatureCollection","features":[';
$geojson_end = ']}';

file_put_contents( $output . $file_name, $geojson_start );


foreach ( $list as $index => $chunk ) {

    $sql_prepared = dt_array_to_sql($chunk);

    $query_raw1 = mysqli_query( $con,
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
                FROM location_grid as lg 
                JOIN location_grid_geometry as g ON g.grid_id=lg.grid_id 
                LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
                LEFT JOIN location_grid as a1 ON lg.admin1_grid_id=a1.grid_id
                LEFT JOIN location_grid as a2 ON lg.admin2_grid_id=a2.grid_id
                LEFT JOIN location_grid as a3 ON lg.admin3_grid_id=a3.grid_id
                LEFT JOIN location_grid as a4 ON lg.admin4_grid_id=a4.grid_id
                LEFT JOIN location_grid as a5 ON lg.admin5_grid_id=a5.grid_id
               WHERE lg.grid_id IN ({$sql_prepared})" )
    ;
    if ( empty( $query_raw1 ) ) {
        print_r( $con );
        die();
    }
    $query1 = mysqli_fetch_all( $query_raw1, MYSQLI_ASSOC );

    /* Feature collection */
    $features = [];
    foreach( $query1 as $result ) {

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

    file_put_contents( $output . $file_name, $features, FILE_APPEND );

    print 'Chunk ' . $index . PHP_EOL;

}

file_put_contents( $output . $file_name, $geojson_end, FILE_APPEND );

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

print 'END' . PHP_EOL;