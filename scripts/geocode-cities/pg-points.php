<?php
/**********************************************************************************************************************
 * Lift the memory limit
 **********************************************************************************************************************/
ini_set('memory_limit', '50000M');

/**********************************************************************************************************************
 * Load $script_con var
 **********************************************************************************************************************/
if ( ! file_exists( 'connect_params.json') ) {
    $script_content = '{"host": "","username": "","password": "","database": ""}';
    file_put_contents( 'connect_params.json', $script_content );
}
$params = json_decode( file_get_contents( "connect_params.json" ), true );
if ( empty( $params['host'] ) ) {
    print 'You have just created the connect_params.json file, but you still need to add database connection information.
Please, open the connect_params.json file and add host, username, password, and database information.' . PHP_EOL;
    die();
}
$script_con = mysqli_connect( $params['host'], $params['username'], $params['password'], 'location_grid');
if (!$script_con) {
    echo 'mysqli Connection FAILED. Check parameters inside connect_params.json file.' . PHP_EOL;
    die();
}
/**********************************************************************************************************************
 * End $script_con var
 **********************************************************************************************************************/
include_once( '/Users/chris/Documents/PROJECTS/localhost/wp-content/plugins/location-grid-project/scripts/vendor/phayes/geophp/geoPHP.inc');
/**********************************************************************************************************************
 * Load Geocoder
 **********************************************************************************************************************/
include('./lg-geocoder-v2.php');
$geocoder = new Location_Grid_Geocoder();
/**********************************************************************************************************************
 * End Geocoder
 **********************************************************************************************************************
 **********************************************************************************************************************/





/**********************************************************************************************************************
 *
 * START CODING SCRIPT
 *
 **********************************************************************************************************************/

$table_lg = 'location_grid';
$table_lgg = 'location_grid_geometry';
$table_pg = 'location_grid_people_groups';
$geometry_folder = '/Users/chris/Documents/LOCATION-GRID-MIRROR/v2/location-grid-mirror-v2/high/';

$results = mysqli_query( $script_con, "SELECT * FROM {$table_pg}");
$query = mysqli_fetch_all( $results, MYSQLI_ASSOC );

if ( empty( $query ) ){
    print 'No Results '. PHP_EOL;
    return;
}

$temp_features = [];
foreach ($query as $index => $row ) {
    if ( 'N' == $row['LeastReached'] ) {
        continue;
    }
    $temp_features[] = array(
        'type' => 'Feature',
        'properties' => $row,
        'geometry' => [
            "type" => "Point",
            "coordinates" => [
                (float) $row['longitude'],
                (float) $row['latitude'],
            ]
        ]
    );

//    print $row['id'] . PHP_EOL;
}

$temp_geojson = array(
    'type' => 'FeatureCollection',
    'features' => $temp_features,
);
$temp_geojson = json_encode( $temp_geojson );
file_put_contents( 'people-groups-least.geojson', $temp_geojson );



//try {
//    $polygon = geoPHP::load( $temp_geojson , 'json');
//} catch ( Exception $e ) {
//    print date('H:i:s') . ' | Fail ' . '(' . $row['grid_id'] . ')' . PHP_EOL;
//}
//
//$polygon->out('json');

print 'End'. PHP_EOL;

mysqli_close($script_con);


//    $grid_row = $geocoder->get_grid_id_by_lnglat( $row['longitude'], $row['latitude'] );
//    if ( empty( $grid_row ) ) {
//        print 'Not found ' . $row['id'] . PHP_EOL;
//        continue;
//    }
//    mysqli_query( $script_con, "UPDATE {$table} SET `grid_id_4770` = '{$grid_row['grid_id']}' WHERE `id` = {$row['id']};");