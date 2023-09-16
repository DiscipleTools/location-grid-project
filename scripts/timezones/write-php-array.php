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
        FROM timezones lg
       
            " );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

print 'Total Count' . PHP_EOL;
print count($query) . PHP_EOL;

$array_string = '$timezones = [';
foreach( $query as $row ) {
    $array_string .= PHP_EOL . '    "' . $row['name'] . '" => [';
    $array_string .= PHP_EOL . '        "timezone" => "' . $row['name'] . '",';
    $array_string .= PHP_EOL . '        "gmt_offset" => "' . $row['offset'] . '",';
    $array_string .= PHP_EOL . '        "dst_offset" => "' . $row['offset_dst'] . '",';
    $array_string .= PHP_EOL . '    ],';
}
$array_string .= PHP_EOL.'];';

file_put_contents( $output . 'timezones.php', $array_string );