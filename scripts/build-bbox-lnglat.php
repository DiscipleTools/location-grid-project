<?php
require_once 'con.php';

$table = $argv[1];
/**
 * Build bbox and lng/lat
 */
include_once( getcwd() . '/vendor/phayes/geophp/geoPHP.inc'); // make sure to run $ composer install on the command line

$admin2_query = mysqli_query( $con, "
        SELECT * FROM {$table} 
    " );
if ( empty( $admin2_query ) ) {
    print_r( $con );
    die();
}
$admin2 = mysqli_fetch_all( $admin2_query, MYSQLI_ASSOC );
foreach( $admin2 as $result ) {
    $grid_id = $result['grid_id'];

    if ( empty($result['geoJSON']) ) {
        print date('H:i:s') . ' | No geoJSON file. ' . $result['name'] . PHP_EOL;
        continue;
    }

    try {
        $polygon = geoPHP::load($result['geoJSON'], 'json');
    } catch ( Exception $e ) {
        print date('H:i:s') . ' | Fail ' . '(' . $grid_id . ' - ' . $result['name'] . ')' . PHP_EOL;
        continue;
    }

    $box = $polygon->getBBox();
    $nla = $box['maxy'];
    $sla = $box['miny'];
    $elo = $box['maxx'];
    $wlo = $box['minx'];

    $centroid = $polygon->centroid();
    $lng = $centroid->coords[0];
    $lat = $centroid->coords[1];

    /* Update db */
    $update = mysqli_query( $con, "UPDATE {$table} SET
        north_latitude={$nla},
        south_latitude={$sla},
        east_longitude={$elo},
        west_longitude={$wlo},
        latitude={$lat},
        longitude={$lng}
        WHERE grid_id = {$grid_id};");


    if ( empty( $update ) ) {
        print_r($con);
        die();
    }
}

print 'Success bbox and lng/lat' . PHP_EOL;

/**
 *
 */

mysqli_close($con );