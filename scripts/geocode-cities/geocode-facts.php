<?php
/**********************************************************************************************************************
 * Lift the memory limit
 **********************************************************************************************************************/
ini_set('memory_limit', '50000M');

/**********************************************************************************************************************
 * Load $con var
 **********************************************************************************************************************/
if ( ! file_exists( 'connect_params.json') ) {
    $content = '{"host": "","username": "","password": "","database": ""}';
    file_put_contents( 'connect_params.json', $content );
}
$params = json_decode( file_get_contents( "connect_params.json" ), true );
if ( empty( $params['host'] ) ) {
    print 'You have just created the connect_params.json file, but you still need to add database connection information.
Please, open the connect_params.json file and add host, username, password, and database information.' . PHP_EOL;
    die();
}
$con = mysqli_connect( $params['host'], $params['username'], $params['password'], $params['database']);
if (!$con) {
    echo 'mysqli Connection FAILED. Check parameters inside connect_params.json file.' . PHP_EOL;
    die();
}
/**********************************************************************************************************************
 * End $con var
 **********************************************************************************************************************/

/**********************************************************************************************************************
 * Load Geocoder
 **********************************************************************************************************************/
include('../lg-geocoder-v2.php');
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

$table = 'location_grid_facts';

$results = mysqli_query( $con, "SELECT * FROM {$table} WHERE full_name IS NULL");
$query = mysqli_fetch_all( $results, MYSQLI_ASSOC );

if ( empty( $query ) ){
    print 'No Results '. PHP_EOL;
    return;
}

foreach ($query as $index => $row ) {

    $drill_down = $geocoder->get_drilldown_by_grid_id($row['grid_id']); print_r($drill_down);
    $full_name = $geocoder->_format_full_name($drill_down);

    mysqli_query( $con, "UPDATE {$table} SET `full_name` = '{$full_name}' WHERE `grid_id` = {$row['grid_id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin0_name` = '{$drill_down['admin0_name']}' WHERE `grid_id` = {$row['grid_id']};");

    mysqli_query( $con, "UPDATE {$table} SET `admin0_grid_id` = '{$drill_down['admin0_grid_id']}' WHERE `grid_id` = {$row['grid_id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin1_grid_id` = '{$drill_down['admin1_grid_id']}' WHERE `grid_id` = {$row['grid_id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin2_grid_id` = '{$drill_down['admin2_grid_id']}' WHERE `grid_id` = {$row['grid_id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin3_grid_id` = '{$drill_down['admin3_grid_id']}' WHERE `grid_id` = {$row['grid_id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin4_grid_id` = '{$drill_down['admin4_grid_id']}' WHERE `grid_id` = {$row['grid_id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin5_grid_id` = '{$drill_down['admin5_grid_id']}' WHERE `grid_id` = {$row['grid_id']};");

    print $full_name. PHP_EOL;

}

print 'End'. PHP_EOL;

mysqli_close($con);