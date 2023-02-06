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
$colorado = json_decode( file_get_contents('Colorado_Census_Tract_Boundaries.geojson' ), true );

include('./../lg-geocoder-v2.php');
$geocoder = new Location_Grid_Geocoder();

$total = 0;
$json = [];
$features = [];
$point_features = [];
$query_raw = mysqli_query( $con,
    "SELECT * FROM location_grid WHERE parent_id = 100364205" );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$counties = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );
$county_count = [];
$county_total = [];
print 'BEGIN COUNTIES'.PHP_EOL;
foreach( $counties as $county ) {
    if( !isset( $county_count[$county['grid_id']] ) ) {
        $county_count[$county['grid_id']] = 0;
    }
    if( !isset( $county_total[$county['grid_id']] ) ) {
        $county_total[$county['grid_id']] = 0;
    }
    $county_total[$county['grid_id']]++;
    print '.';
}

print PHP_EOL.'BEGIN TOTALS'.PHP_EOL;
// Build totals per county
foreach( $colorado['features'] as $result ) {
    $temp_features = [];
    $temp_features[] = array(
        'type' => 'Feature',
        'id' => $total,
        'properties' => [
            'fips_id' => $result['properties']['OBJECTID'],
            'fips' => $result['properties']['FIPS'],
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

    $centroid = $polygon->centroid();
    $lng = $centroid->coords[0];
    $lat = $centroid->coords[1];

    $grid_row= $geocoder->get_grid_id_by_lnglat( $lng, $lat, NULL, 2 );

    if( empty( $grid_row ) ) {
        print date('H:i:s') . ' | Fail ' . '(' . $result['properties']['NBHD_NAME'] . ')' . PHP_EOL;
        continue;
    }

    $county_total[$grid_row['grid_id']]++;
    print '.'.PHP_EOL;
}

print PHP_EOL.'BEGIN EXPORT'.PHP_EOL;
// Build features and export geojson
foreach( $colorado['features'] as $result ) {
    $total++;

    $temp_features = [];
    $temp_features[] = array(
        'type' => 'Feature',
        'id' => $total,
        'properties' => [
            'fips_id' => $result['properties']['OBJECTID'],
            'fips' => $result['properties']['FIPS'],
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

    $grid_row= $geocoder->get_grid_id_by_lnglat( $lng, $lat, NULL, 2 );

    $full_name = _full_name($grid_row);

    $name = $result['properties']['FIPS'];

    $county_count[$grid_row['grid_id']]++;

    // build full json and individual properties of the feature
    $json[] = $properties = [
        'grid_id' => $grid_row['grid_id'],
        "full_name" =>  $grid_row['admin2_name'] . ' County, '. $grid_row['admin1_name']. ' (Tract '.$county_count[$grid_row['grid_id']] . ' of '.$county_total[$grid_row['grid_id']].')',
        "name" => $grid_row['admin2_name'] . ' County, '. $grid_row['admin1_name']. ', #'.$county_count[$grid_row['grid_id']],
        "level" => $grid_row['level'],
        "level_name" => $grid_row['level_name'],
        "country_code" => $grid_row['country_code'],
        "admin0_code" => $grid_row['admin0_code'],
        "parent_id" => $grid_row['parent_id'],
        "admin0_grid_id" => $grid_row['admin0_grid_id'],
        "admin1_grid_id" => $grid_row['admin1_grid_id'],
        "admin2_grid_id" => $grid_row['admin2_grid_id'],
        "admin3_grid_id" => '',
        "admin4_grid_id" => '',
        "admin5_grid_id" => '',
        "longitude" => $lng,
        "latitude" => $lat,
        "north_latitude" => $nla,
        "south_latitude" => $sla,
        "east_longitude" => $elo,
        "west_longitude" => $wlo,
        'fips' => $result['properties']['FIPS'],
    ];

    // add to new feature
    $single_geojson = array(
        'type' => 'FeatureCollection',
        'features' => [
            [
                'type' => 'Feature',
                'id' => $total,
                'properties' => $properties,
                'geometry' => $result['geometry'],
            ]
        ]
    );
    file_put_contents( './output/colorado_'.$grid_row['grid_id'].'_'.$result['properties']['FIPS'].'.geojson', json_encode( $single_geojson ) );

    print '.';
//    break;
}

mysqli_close($con);

print PHP_EOL. 'TOTAL: ' . $total . PHP_EOL;
print 'END' . PHP_EOL;