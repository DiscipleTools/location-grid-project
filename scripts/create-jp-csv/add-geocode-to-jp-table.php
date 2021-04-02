<?php
/**
 * This geocodes
 */

include('con.php');
include('location-grid-geocoder-non-wp.php');

$geocoder = new Location_Grid_Geocoder();

$table = 'jp_people_groups';

$results = mysqli_query( $con, "SELECT * FROM {$table}");
$query = mysqli_fetch_all( $results, MYSQLI_ASSOC );

if ( empty( $query ) ){
    print 'No Results '. PHP_EOL;
    return;
}

foreach ($query as $row ) {

    $grid_row = $geocoder->get_grid_id_by_lnglat( $row['longitude'], $row['latitude'] , null, 2 );
    if ( empty( $grid_row ) ){
        $grid_row = $geocoder->get_grid_id_by_lnglat( $row['longitude'], $row['latitude'] , null, 1 );
    }
    else if ( empty( $grid_row ) ){
        $grid_row = $geocoder->get_grid_id_by_lnglat( $row['longitude'], $row['latitude'] , null, 0 );
    }
    else if ( empty( $grid_row ) ) {
        continue;
    }
    $name = $geocoder->_format_full_name( $grid_row );
    if ( empty( $name ) ) {
        $name = $grid_row['name'];
    }

    $grid_id = $grid_row['admin2_grid_id'] ?? $grid_row['admin1_grid_id'] ?? $grid_row['admin0_grid_id'];
    if ( empty( $grid_row['admin1_grid_id'] )  ){
        $parent_id = 1;
        $level = 'admin0';
    }
    else if ( empty( $grid_row['admin2_grid_id']  ) ) {
        $parent_id = $grid_row['admin0_grid_id'];
        $level = 'admin1';
    }
    else {
        $parent_id = $grid_row['admin1_grid_id'];
        $level = 'admin2';
    }

    mysqli_query( $con, "UPDATE {$table} SET `level` = '{$level}' WHERE `id` = {$row['id']};");
    mysqli_query( $con, "UPDATE {$table} SET `grid_id` = '{$grid_id}' WHERE `id` = {$row['id']};");
    mysqli_query( $con, "UPDATE {$table} SET `parent_id` = '{$parent_id}' WHERE `id` = {$row['id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin0_grid_id` = '{$grid_row['admin0_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin1_grid_id` = '{$grid_row['admin1_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $con, "UPDATE {$table} SET `admin2_grid_id` = '{$grid_row['admin2_grid_id']}' WHERE `id` = {$row['id']};");
    mysqli_query( $con, "UPDATE {$table} SET `label` = '{$name}' WHERE `id` = {$row['id']};");

    print $name. PHP_EOL;
}

print 'End'. PHP_EOL;