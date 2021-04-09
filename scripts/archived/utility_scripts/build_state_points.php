<?php
/**
 * NOTES
 *
 *
 */
require_once( 'con.php' );

// Command Line Usage
// $ php build_state_polygons.php

print date('H:i:s') . PHP_EOL;

$admin1_results = mysqli_query( $con, "
    SELECT g.geonameid, g.name
    FROM {$tables['geonames']} as g
    WHERE g.level = 'admin1' ;
  " );

$states = mysqli_fetch_all($admin1_results, MYSQLI_ASSOC);
foreach( $states as $state ) {// state loop

    $file = $output['points'] . $state['geonameid'] . '.geojson';
    if ( file_exists( $file ) ) {
        print date('H:i:s') . ' | Skip. ' . $state['name'] . PHP_EOL;
        continue;
    }

    $admin1_results = mysqli_query( $con, "
        SELECT g.geonameid, g.country_code, g.admin1_code, g.admin2_code, g.name, g.latitude, g.longitude
        FROM {$tables['geonames']} as g
        WHERE g.level = 'admin2' AND g.admin1_geonameid = {$state['geonameid']};
      " );

    $i = 0;
    $html = '{"type":"FeatureCollection","features":[';
    while ( $row = $admin1_results->fetch_assoc() ) { // county loop
        if ( 0 != $i ) {
            $html .= ',';
        }
        $html .= '{"type": "Feature","geometry": ';
        $html .= '{"type":"Point","coordinates": ['. (float) $row['longitude'].','. (float) $row['latitude'].']}';

        $html .= ',"properties":{';
        $html .= '"name":"' . $row[ 'name' ] . '",';
        $html .= '"id":"' . (int) $row[ 'geonameid' ] . '",';
        $html .= '"country_code":"' . $row[ 'country_code' ] . '",';
        $html .= '"admin1_code":"' . $row[ 'admin1_code' ] . '",';
        $html .= '"admin2_code":"' . $row[ 'admin2_code' ] . '",';
        $html .= '"center_lat":' . (float) $row[ 'latitude' ] . ',';
        $html .= '"center_lng":' . (float) $row[ 'longitude' ] . ',';
        $html .= '"geonameid":' . (int) $row[ 'geonameid' ];
        $html .= '}';
        $html .= ',"id":"' . $row[ 'geonameid' ] . '"';

        $html .= '}';

        $i++;
    }
    $html .= ']}';

    $response = file_put_contents( $file, $html . PHP_EOL );

    if ( $response > 0 ) {
        print date( 'H:i:s' ) . ' | Success. ' . $state[ 'name' ] . PHP_EOL;
    } else {
        print date( 'H:i:s' ) . ' | No luck. No data written to the ' . $state[ 'name' ] . ' file';
    }
}



print '
**********************************************************************
*                                                                    *
*                           COMPLETE                                 *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL;

print date('H:i:s') . PHP_EOL;

mysqli_close($con);
