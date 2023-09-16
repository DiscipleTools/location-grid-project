<?php
// Builds a balanced states view of the world.
include('con.php');

print 'BEGIN' . PHP_EOL;
$output = getcwd() . '/output/';
if( ! is_dir( $output ) ) {
    mkdir( $output, 0755, true);
}

$query_raw = mysqli_query( $con,
    "
        SELECT lg.* 
        FROM location_grid_timezones lg
        JOIN dt_location_grid_zume_v2 lv2 ON lv2.grid_id=lg.grid_id
        WHERE timezone IS NULL
            " );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

print 'Total Count' . PHP_EOL;
print count($query) . PHP_EOL;

$block = 0;
foreach( $query as $row ) {

    $json = shell_exec("curl 'http://api.geonames.org/timezoneJSON?lat=".$row['latitude']."&lng=".$row['longitude']."&username=discipletools' " );
//    print date('H:i:s') . ' | ' . $row['grid_id'] . PHP_EOL;
    print_r($json);
    $json = json_decode( $json, true );

    mysqli_query( $con,"UPDATE location_grid_timezones SET timezone = '" . $json['timezoneId'] . "', gmt_offset = '" . $json['gmtOffset'] . "', dst_offset = '" . $json['dstOffset'] . "' WHERE grid_id = " . $row['grid_id'] . ";");

    if ( $block > 950 ) {
        print   PHP_EOL . '**************************' . PHP_EOL . 'Sleeping' . PHP_EOL;
        sleep( 3700 );
        $block = 0;
    }
    $block++;
}