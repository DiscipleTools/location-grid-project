<?php
ini_set('memory_limit', '500000000M');
/**
 * Build geojson tiles
 */

// argv[1] = file name
// argv[2] = block size (int)

//include_once( '../vendor/phayes/geophp/geoPHP.inc' ); // make sure to run $ composer install on the command line
$world_geojson = json_decode( file_get_contents('./output/simplified_output/'. $argv[1]), true );

if ( empty( $world_geojson ) ){
    die();
}

$tile_block = $argv[2];
if ( isset( $argv[1] ) ) {
    $tile_block = $argv[1];
}

$removed = 0;
$total_features = 0;

$file_name = 1;
$i = 0;
$total_i = 0;
$features_count = count( $world_geojson['features']);
$new_feature_set = [];

foreach($world_geojson['features'] as $feature ){
    if (empty( $feature['geometry'] ) ) {
        print $feature['properties']['grid_id'] . ' | '. $feature['properties']['full_name'] . PHP_EOL;
        print $file_name .  '.geojson'. PHP_EOL;
        $removed++;
        continue;
    }
    $new_feature_set[] = $feature;

    $i++;
    $total_i++;
    $total_features++;
    if ( $tile_block <= $i || $features_count == $total_i ){

        $geojson = array(
            'type' => "FeatureCollection",
            'features' => $new_feature_set,
        );
        $geojson = json_encode( $geojson );

        file_put_contents( './output/' . $file_name .  '.geojson', $geojson );
        $i = 0;
        $file_name++;
        $new_feature_set = [];

    }

    print $feature['properties']['full_name'] . PHP_EOL;

}
print 'Features processed: ' . $total_features . PHP_EOL;
print $removed . PHP_EOL;
print 'END' . PHP_EOL;

