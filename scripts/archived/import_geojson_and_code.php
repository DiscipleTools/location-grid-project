<?php

/**
 * Command Line
 *
 * $ php import_geojson_and_code.php
 */

require_once 'con.php';

$upload_file_name = 'usaLow.json';
$output_grid_id = 100364199;


$file = $output['import'] . $upload_file_name;
$newfile = $output['import'] . $output_grid_id . '.geojson';

$geojson = json_decode( file_get_contents( $file ), true );

$query = mysqli_query( $con, "
        SELECT g.*
        FROM {$tables['geonames']} as g
        WHERE g.grid_id = $output_grid_id OR g.parent_id = $output_grid_id;
      " );
$items = mysqli_fetch_all( $query, MYSQLI_ASSOC );
$list = [];
foreach( $items as $item ) {
    $match_element = $item['name'];
    $list[$match_element] = $item;
}

foreach ( $geojson['features'] as $index => $feature ) {
    if ( isset( $list[$feature['properties']['name']] ) ) {
        print $feature['properties']['name'] . PHP_EOL;

        $geojson['features'][$index]['id'] = (string) $list[$feature['properties']['name']]['grid_id'];
        $geojson['features'][$index]['properties'] = [
            'name' => (string) $list[$feature['properties']['name']]['name'],
            'id' => (string) $list[$feature['properties']['name']]['grid_id'],
            'grid_id' => (int) $list[$feature['properties']['name']]['grid_id'],
            'country_code' => (string) $list[$feature['properties']['name']]['country_code'],
            'admin0_code' => (string) $list[$feature['properties']['name']]['admin0_code'],
            'center_lat' => (float) $list[$feature['properties']['name']]['latitude'],
            'center_lng' => (float) $list[$feature['properties']['name']]['longitude'],
        ];

    } else {
        print $feature['properties']['name'];
        die('not found');
    }
}

$content = file_put_contents( $newfile, json_encode( $geojson ) );
