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
$script_con = mysqli_connect( $params['host'], $params['username'], $params['password'], $params['database']);
if (!$script_con) {
    echo 'mysqli Connection FAILED. Check parameters inside connect_params.json file.' . PHP_EOL;
    die();
}
/**********************************************************************************************************************
 * End $script_con var
 **********************************************************************************************************************/

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

$table = 'location_grid_cities';

$results = mysqli_query( $script_con, "SELECT * FROM {$table} WHERE grid_id_4770 IS NULL");
$query = mysqli_fetch_all( $results, MYSQLI_ASSOC );

if ( empty( $query ) ){
    print 'No Results '. PHP_EOL;
    return;
}
print_r($query);
foreach ($query as $index => $row ) {

    $grid_row = $geocoder->get_grid_id_by_lnglat( $row['longitude'], $row['latitude'] );
    if ( empty( $grid_row ) ) {
        print 'Not found ' . $row['id'] . PHP_EOL;
        continue;
    }
    mysqli_query( $script_con, "UPDATE {$table} SET `grid_id_4770` = '{$grid_row['grid_id']}' WHERE `id` = {$row['id']};");

    print $row['name']. PHP_EOL;

}

print 'End'. PHP_EOL;

mysqli_close($script_con);