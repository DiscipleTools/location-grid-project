<?php

print 'START' . PHP_EOL;

require_once( 'con.php' );

require_once( '../location-grid-geocoder-non-wp.php' );
$geocoder = new Location_Grid_Geocoder();

/* parent id */
if ( isset( $argv[1] ) ) {
    $code = $argv[1];
} else {
    print 'admin0_code argument missing' . PHP_EOL;
    die();
}

$query  = mysqli_query( $con, "
            SELECT * FROM location_grid WHERE admin0_grid_id IN ({$code})" );
$results = mysqli_fetch_all( $query, MYSQLI_ASSOC );

foreach ($results as $result ) {
    $lng = $result['longitude'];
    $lat = $result['latitude'];

    if ( $result['level'] >= 1 ) {
        $admin1 = $geocoder->get_grid_id_by_lnglat($lng, $lat, 'MA', 1);
        mysqli_query($con, "UPDATE location_grid SET admin1_grid_id = {$admin1['grid_id']} WHERE grid_id = {$result['grid_id']}");
    }

    if ( $result['level'] >= 2 ){
        $admin2 = $geocoder->get_grid_id_by_lnglat($lng,$lat, 'MA', 2 );
        mysqli_query( $con, "UPDATE location_grid SET admin2_grid_id = {$admin2['grid_id']} WHERE grid_id = {$result['grid_id']}" );
    }

    if ( $result['level'] >= 3 ) {
        $admin3 = $geocoder->get_grid_id_by_lnglat($lng, $lat, 'MA', 3);
        mysqli_query($con, "UPDATE location_grid SET admin3_grid_id = {$admin3['grid_id']} WHERE grid_id = {$result['grid_id']}");
    }

    if ( $result['level'] >= 4 ) {
        $admin4 = $geocoder->get_grid_id_by_lnglat($lng, $lat, 'MA', 4);
        mysqli_query($con, "UPDATE location_grid SET admin4_grid_id = {$admin4['grid_id']} WHERE grid_id = {$result['grid_id']}");
    }

    if ( 2 == $result['level'] ) {
        mysqli_query($con, "UPDATE location_grid SET parent_id = {$admin1['grid_id']} WHERE grid_id = {$result['grid_id']}");
    }
    else if ( 3 == $result['level'] ) {
        mysqli_query($con, "UPDATE location_grid SET parent_id = {$admin2['grid_id']} WHERE grid_id = {$result['grid_id']}");
    }
    else if ( 4 == $result['level'] ) {
        mysqli_query($con, "UPDATE location_grid SET parent_id = {$admin3['grid_id']} WHERE grid_id = {$result['grid_id']}");
    }


    print '|a1 '.$admin1['grid_id'] . '|a2 '.$admin2['grid_id'] . '|a3 '.$admin3['grid_id'] . '|a4 '.$admin4['grid_id']. ' ( '.$result['name'].' - '.$result['grid_id'].')' . PHP_EOL;

}



print 'END' . PHP_EOL;

