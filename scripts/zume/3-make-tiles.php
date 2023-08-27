<?php
ini_set('memory_limit', '500000000M');

// argv[1] = file name
// argv[2] = block size (int)

//include_once( '../vendor/phayes/geophp/geoPHP.inc' ); // make sure to run $ composer install on the command line
$world_geojson = json_decode( file_get_contents('./output/simplified_output/'. $argv[1]), true );
if ( empty( $world_geojson ) ){
    die();
}
$tile_block = 2000;
if ( isset( $argv[2] ) ) {
    $tile_block = $argv[2];
}
$target_folder = './output/tiles/';
if( ! is_dir( $target_folder ) ) {
    mkdir( $target_folder, 0755, true);
}

$world_geojson_features = array_chunk( $world_geojson['features'], $tile_block );

foreach( $world_geojson_features as $file_id => $chunk ) {
    $file_id++;
    $new_feature_set = [];

    foreach ( $chunk as $feature) {
        if (empty($feature['geometry'])) {
            print $feature['properties']['grid_id'] . ' | ' . $feature['properties']['full_name'] . PHP_EOL;
            continue;
        }
        $new_feature_set[] = $feature;

        print $feature['properties']['full_name'] . PHP_EOL;
    }

    $geojson = array(
        'type' => "FeatureCollection",
        'features' => $new_feature_set,
    );
    $geojson = json_encode($geojson);

    file_put_contents('./output/tiles/' . $file_id . '.geojson', $geojson);
}
print 'END' . PHP_EOL;