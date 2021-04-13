<?php
ini_set('memory_limit', '500000M');
/**
 * Build geojson tiles
 */
//include_once( '../vendor/phayes/geophp/geoPHP.inc' ); // make sure to run $ composer install on the command line
$world_geojson = json_decode( file_get_contents('./output/simplified_output/world.geojson'), true );

if ( empty( $world_geojson ) ){
    die();
}

$tile_block = 2000;

$removed = 0;

$file_name = 1;
$i = 0;
$new_feature_set = [];
foreach($world_geojson['features'] as $feature ){
    if (empty( $feature['geometry'] ) ) {
        print_r( $feature['geometry'] );
        print $feature['properties']['grid_id'] . ' | '. $feature['properties']['full_name'] . PHP_EOL;
        print $file_name .  '.geojson'. PHP_EOL;
        $removed++;
        continue;
    }
    $new_feature_set[] = $feature;

    $i++;
    if ( $tile_block <= $i ){

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
print $removed . PHP_EOL;
print 'END' . PHP_EOL;

