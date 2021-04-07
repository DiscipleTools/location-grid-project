<?php
print 'Start' . PHP_EOL;

require_once( 'con.php' );

require_once( '../location-grid-geocoder-non-wp.php' );
$geocoder = new Location_Grid_Geocoder();

$query  = mysqli_query( $con, "
            SELECT * FROM morocco_import WHERE ADM2_EN != ''" );
$results = mysqli_fetch_all( $query, MYSQLI_ASSOC );

foreach( $results as $result ) {
    $row = $geocoder->get_grid_id_by_lnglat($result['longitude'], $result['latitude'], null, 2 );

    print $result['name'] . ': ' . $row['grid_id'] . ' -  ' . $row['name'] . ' - ' . $row['level'] . PHP_EOL;
}

mysqli_close($con);
print 'End' . PHP_EOL;