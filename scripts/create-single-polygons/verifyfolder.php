<?php
require_once( 'con.php' );

print 'BEGIN' . PHP_EOL;

/** FOLDERS */
$output = [
    'output' => getcwd() . '/output/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

/* parent id */
$folder = getcwd() . '/output/';
if ( isset( $argv[1] ) ) {
    $folder = $argv[1];
}

$query_raw = mysqli_query( $con,
    "SELECT lg.grid_id FROM location_grid as lg" );

if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

foreach( $query as $item ){
   if ( ! file_exists( $folder . $item['grid_id'] . '.geojson' ) ) {
       print $folder . $item['grid_id'] . '.geojson' . PHP_EOL;
       die();
   }
   $contents = file_get_contents($folder . $item['grid_id'] . '.geojson');
   $contents = json_decode($contents, TRUE );

   if ( ! isset( $contents['features'][0]['properties']['full_name'] ) ) {
       print $item['grid_id'] . PHP_EOL;
   }

}

print 'END' . PHP_EOL;