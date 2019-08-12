<?php

require_once( 'con.php' );

$mapbox_key = 'pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA';

require_once( 'location-grid-geocoder.php' );
$geocoder = new Location_Grid_Geocoder();

print date('H:i:s') . ' | Begin' . PHP_EOL;

$results_object = mysqli_query( $con, "
        SELECT address, entityid
        FROM colorado_active_churches
        WHERE geonameid IS NULL;
    " );
$results = mysqli_fetch_all($results_object, MYSQLI_ASSOC);

foreach ( $results as $row ) {

    $json = shell_exec('mapbox --access-token='.$mapbox_key. ' geocoding \''.$row['address'].'\''  );
    $json = json_decode( $json, true );

    $longitude = $json['features'][0]['center'][0];
    $latitude = $json['features'][0]['center'][1];

    $sql = "UPDATE colorado_active_churches SET longitude={$longitude}, latitude={$latitude} WHERE entityid={$row['entityid']}";

    $added = mysqli_query( $con, $sql);

    $geonameid = $geocoder->get_geonameid_by_lnglat( $longitude, $latitude );
    if ( empty( $geonameid ) ) {
        print $row['entityid'] . ' FAIL' . PHP_EOL;
        continue;
    }

    $sql = "UPDATE colorado_active_churches SET geonameid={$geonameid} WHERE entityid={$row['entityid']}";
    $added = mysqli_query( $con, $sql);

    print $row['entityid'] . PHP_EOL;


}

print date('H:i:s') . ' | End' . PHP_EOL;