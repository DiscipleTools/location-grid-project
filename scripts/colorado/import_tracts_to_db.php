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
$colorado = json_decode( file_get_contents('tl_2022_08_tract.geojson' ), true );

include('./../lg-geocoder-v2.php');
$geocoder = new Location_Grid_Geocoder();

$total = 0;
$county_count = [];
$tract_grid_id = 200000000;

print 'BEGIN EXPORT'.PHP_EOL;
// Build features and export geojson
foreach( $colorado['features'] as $result ) {
    $total++;

    $temp_features = [];
    $temp_features[] = array(
        'type' => 'Feature',
        'id' => $total,
        'properties' => [
            'fips_id' => $result['properties']['GEOID'],
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
        print date('H:i:s') . ' | Fail ' . '(' . $result['properties']['GEOID'] . ')' . PHP_EOL;
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

    if( !isset( $county_count[$grid_row['grid_id']] ) ) {
        $county_count[$grid_row['grid_id']] = 0;
    }
    $county_count[$grid_row['grid_id']]++;
    $name = $grid_row['name'] . ' County (Tract '.$result['properties']['NAME'].')';


    mysqli_query( $con, "
    INSERT INTO location_grid 
        (
             `grid_id`,
              `name` ,
              `level`,
              `level_name`,
              `country_code`,
              `admin0_code`,
              `admin1_code`,
              `admin2_code`,
              `admin3_code`,
              `admin4_code`,
              `admin5_code`,
              `parent_id`,
              `admin0_grid_id`,
              `admin1_grid_id`,
              `admin2_grid_id`,
              `admin3_grid_id`,
              `admin4_grid_id`,
              `admin5_grid_id`,
              `longitude`,
              `latitude`,
              `north_latitude`,
              `south_latitude`,
              `east_longitude`,
              `west_longitude`,
              `population`,
              `population_date`,
              `modification_date`,
              `geonames_ref`,
              `wikidata_ref`                             
         )
        VALUES (
              '".$tract_grid_id."',
              '".$name."',
              3,
              'tract',
              '".$grid_row['country_code']."',
              '".$grid_row['admin0_code']."',
              '".$result['properties']['STATEFP']."',
              '".$result['properties']['COUNTYFP']."',
             '".$result['properties']['TRACTCE']."',
              NULL,
              NULL,
              '".$grid_row['admin2_grid_id']."',
               '".$grid_row['admin0_grid_id']."',
               '".$grid_row['admin1_grid_id']."',
               '".$grid_row['admin2_grid_id']."',
              NULL,
              NULL,
              NULL,
               '".$lng."',
              '".$lat."',
             '".$nla."',
               '".$sla."',
               '".$elo."',
               '".$wlo."',
              5000,
              '2022-02-01',
              '2022-02-01',
              ".$result['properties']['GEOID'].",
              NULL
        )
    " );

    mysqli_query( $con, "
    INSERT INTO location_grid_geometry 
        (
             `grid_id`,
              `geoJSON`                           
         )
        VALUES (
              '".$tract_grid_id."',
              '".json_encode( $result['geometry'] )."'
        )
    " );

    $tract_grid_id++;
    print '.';


}

mysqli_close($con);

print PHP_EOL. 'TOTAL: ' . $total . PHP_EOL;
print 'END' . PHP_EOL;