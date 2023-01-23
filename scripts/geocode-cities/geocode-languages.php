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

$table = 'location_grid_people_groups';

$results = mysqli_query( $script_con, "SELECT * FROM {$table} WHERE lg_full_name IS NULL");
$query = mysqli_fetch_all( $results, MYSQLI_ASSOC );

if ( empty( $query ) ){
    print 'No Results '. PHP_EOL;
    return;
}

foreach ($query as $index => $row ) {

    $grid_row = $geocoder->get_grid_id_by_lnglat( $row['longitude'], $row['latitude'] );
    if ( empty( $grid_row ) ) {
        print 'Not found ' . $row['id'] . PHP_EOL;
        continue;
    }

    $full_name = $geocoder->_format_full_name($grid_row);
    $drill_down = $geocoder->get_drilldown_by_grid_id($grid_row['grid_id']);

    mysqli_query( $script_con, "UPDATE {$table} SET `lg_name` = '{$grid_row['name']}' WHERE `id` = {$row['id']};");
    mysqli_query( $script_con, "UPDATE {$table} SET `lg_full_name` = '{$full_name}' WHERE `id` = {$row['id']};");
    mysqli_query( $script_con, "UPDATE {$table} SET `admin0_name` = '{$drill_down['admin0_name']}' WHERE `id` = {$row['id']};");

    mysqli_query( $script_con, "UPDATE {$table} SET `admin0_grid_id` = '{$grid_row['admin0_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $script_con, "UPDATE {$table} SET `admin1_grid_id` = '{$grid_row['admin1_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $script_con, "UPDATE {$table} SET `admin2_grid_id` = '{$grid_row['admin2_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $script_con, "UPDATE {$table} SET `admin3_grid_id` = '{$grid_row['admin3_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $script_con, "UPDATE {$table} SET `admin4_grid_id` = '{$grid_row['admin4_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $script_con, "UPDATE {$table} SET `admin5_grid_id` = '{$grid_row['admin5_grid_id']}' WHERE `id` = {$row['id']};");

    print $full_name. PHP_EOL;

}

print 'End'. PHP_EOL;

mysqli_close($script_con);