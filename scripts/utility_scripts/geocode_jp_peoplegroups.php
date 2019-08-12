<?php

require_once( 'con.php' );

require_once( 'location-grid-geocoder.php' );
$geocoder = new Location_Grid_Geocoder();

print date('H:i:s') . ' | Begin' . PHP_EOL;

$results_object = mysqli_query( $con, "
        SELECT id, Latitude, Longitude
        FROM jp_people_groups_with_geoname
        WHERE geonameid IS NULL;
    " );
$results = mysqli_fetch_all($results_object, MYSQLI_ASSOC);

foreach ( $results as $row ) {

    $longitude = $row['Longitude'];
    $latitude = $row['Latitude'];

    $geonameid = $geocoder->get_geonameid_by_lnglat( $longitude, $latitude, true );
    if ( empty( $geonameid ) ) {
        print $row['id'] . ' FAIL' . PHP_EOL;
        continue;
    }

    $sql = "UPDATE jp_people_groups_with_geoname SET geonameid={$geonameid} WHERE id={$row['id']}";
    $added = mysqli_query( $con, $sql);

    print date('H:i:s') . ' | ' . $row['id'] . PHP_EOL;

}

print date('H:i:s') . ' | End' . PHP_EOL;