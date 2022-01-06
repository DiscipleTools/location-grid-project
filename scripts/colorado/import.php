<?php
/**
 * This script imports a raw denver neighborhoods polygon and creates a geojson polygon with location grid elements.
 *
 */
include('con.php');

print 'START' . PHP_EOL;

// load mapping tool
include_once( '/Users/chris/Documents/PROJECTS/localhost/wp-content/plugins/location-grid-project/scripts/vendor/phayes/geophp/geoPHP.inc'); // make sure to run $ composer install on the command line

// import source geojson
$colorado = json_decode( file_get_contents('statistical_neighborhoods.geojson' ), true );

// identify parent grid id
$parent_id = 100364508;
$query_raw = mysqli_query( $con,
    "SELECT * FROM dt_location_grid WHERE grid_id = $parent_id" );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$default = mysqli_fetch_array( $query_raw, MYSQLI_ASSOC );

$total = 0;
$json = [];
$features = [];

foreach( $colorado['features'] as $result ) {
    // create name
    $name = $result['properties']['NBHD_NAME'] . ', Denver, Colorado, United States';

    $total++;

    // temp geojson
    $temp_features = [];
    $temp_features[] = array(
        'type' => 'Feature',
        'id' => $total,
        'properties' => [
            'grid_id' => $total,
            'full_name' => $result['properties']['NBHD_NAME'] . ', Denver, Colorado, United States'
        ],
        'geometry' => $result['geometry'],
    );
    $temp_geojson = array(
        'type' => 'FeatureCollection',
        'features' => $temp_features,
    );
    $temp_geojson = json_encode( $temp_geojson );
    try {
        $polygon = geoPHP::load( $temp_geojson , 'json');
    } catch ( Exception $e ) {
        print date('H:i:s') . ' | Fail ' . '(' . $result['properties']['NBHD_NAME'] . ')' . PHP_EOL;
        continue;
    }

    // get bounding box and center points
    $box = $polygon->getBBox();
    $nla = $box['maxy'];
    $sla = $box['miny'];
    $elo = $box['maxx'];
    $wlo = $box['minx'];

    $centroid = $polygon->centroid();
    $lng = $centroid->coords[0];
    $lat = $centroid->coords[1];


    // build full json and individual properties of the feature
    $json[] = $properties = [
        "full_name" => $name,
        "name" => $result['properties']['NBHD_NAME'],
        "level" => $default['level'],
        "level_name" => $default['level_name'],
        "country_code" => $default['country_code'],
        "admin0_code" => $default['admin0_code'],
        "parent_id" => $default['parent_id'],
        "admin0_grid_id" => $default['admin0_grid_id'],
        "admin1_grid_id" => $default['admin1_grid_id'],
        "admin2_grid_id" => $default['admin2_grid_id'],
        "admin3_grid_id" => '',
        "admin4_grid_id" => '',
        "admin5_grid_id" => '',
        "longitude" => $lng,
        "latitude" => $lat,
        "north_latitude" => $nla,
        "south_latitude" => $sla,
        "east_longitude" => $elo,
        "west_longitude" => $wlo,
    ];

    // add to new feature
    $features[] = array(
        'type' => 'Feature',
        'id' => $total,
        'properties' => $properties,
        'geometry' => $result['geometry'],
    );

}

// build full collection geojson
$geojson = array(
    'type' => 'FeatureCollection',
    'features' => $features,
);

file_put_contents( './output/' . $parent_id .  '.geojson', json_encode( $geojson ) );
file_put_contents( './output/_list.json', json_encode( $json ) );

print 'TOTAL: ' . $total . PHP_EOL;
print 'END' . PHP_EOL;