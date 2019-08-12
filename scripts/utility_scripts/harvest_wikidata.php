<?php
/**
 * Build Boundaries
 *
 * bbox = left,bottom,right,top
 * bbox = min Longitude , min Latitude , max Longitude , max Latitude
 */

require_once( 'con.php' );
include_once( getcwd() . '/vendor/phayes/geophp/geoPHP.inc'); // make sure to run $ composer install on the command line

/**
 * BBox Output Map
 * maxy = north_latitude
 * miny = south_latitude
 * maxx = west_longitude
 * minx = east_longitude
 */
/**
 * Command Line Usage
 * Get all results
 * $ php build_boundaries.php
 *
 * Get single geoname result
 * $ php build_boundaries.php geonameid=888888
 */


print date('H:i:s') . ' | Started.' . PHP_EOL;

$results = mysqli_query( $con, "
        SELECT  g.geonameid, g.name, g.country_code, g.level, g.reverse_mapbox_response
        FROM sg_missing_polygons_wiki as g
        WHERE 
        reverse_mapbox_response IS NOT NULL 
        AND wikidata IS NULL
    " );

$raw_list = mysqli_fetch_all($results, MYSQLI_ASSOC);

// Loop for standard admin2
/*
foreach( $raw_list as $l ) {
    $geojson = json_decode( $l['reverse_mapbox_response'], true );

    // set place geojson
    $i = false;
    foreach ( $geojson['features'] as $index => $feature ) {
        if ( substr( $feature['id'],0, 5) === 'place') {
            $i = $index;
            $geojson = $geojson['features'][$index];
        }
    }
    if ( $i === false ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') No "Place" features found.' . PHP_EOL;
        continue;
    }

    // record level is admin2
    if ( $l['level'] !== 'admin2' ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Level" not equal to admin2' . PHP_EOL;
        continue;
    }

    // if context has id.region and id.country
    if ( substr( $geojson['context'][0]['id'],0, 6) !== 'region' ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Region" not found in context.' . PHP_EOL;
        continue;
    }

    // relevance is 1
    if ( $geojson['relevance'] !== 1 ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Relevance" is not equal to 1.' . PHP_EOL;
        continue;
    }

    // extract bbox and install to nswe
    // 0 = south
    // 1 = west
    // 2 = north
    // 3 = east

    if ( ! isset( $geojson['properties']['wikidata'] ) ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "No Wikidata.' . PHP_EOL;
        continue;
    }
    $wikidata = $geojson['properties']['wikidata'];

    $update = mysqli_query( $con, "UPDATE sg_missing_polygons_wiki SET
        wikidata='{$wikidata}'
        WHERE geonameid = {$l['geonameid']};");

    print date('H:i:s') . ' | Finished (' . $l['geonameid'] . ')'. PHP_EOL;
}
*/

// admin3
/*
foreach( $raw_list as $l ) {
    $geojson = json_decode( $l['reverse_mapbox_response'], true );

    // set place geojson
    $i = false;
    foreach ( $geojson['features'] as $index => $feature ) {
        if ( array_search( 'place', $feature['place_type'] ) !== false ) {
            $i = true;
            $geojson = $geojson['features'][$index];
        }
    }
    if ( $i === false ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') No "Place" features found.' . PHP_EOL;
        continue;
    }

    // record level is admin2
    if ( $l['level'] !== 'admin3' ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Level" not equal admin3' . PHP_EOL;
        continue;
    }

    // if context has id.region and id.country
    if ( substr( $geojson['context'][0]['id'],0, 6) !== 'region' ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Region" not found in context.' . PHP_EOL;
        continue;
    }

    // relevance is 1
    if ( $geojson['relevance'] !== 1 ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Relevance" is not equal to 1.' . PHP_EOL;
        continue;
    }

    // extract bbox and install to nswe
    // 0 = south
    // 1 = west
    // 2 = north
    // 3 = east

    if ( ! isset( $geojson['properties']['wikidata'] ) ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "No Wikidata.' . PHP_EOL;
        continue;
    }
    $wikidata = $geojson['properties']['wikidata'];

    $update = mysqli_query( $con, "UPDATE sg_missing_polygons_wiki SET
        wikidata='{$wikidata}'
        WHERE geonameid = {$l['geonameid']};");

    print date('H:i:s') . ' | Finished (' . $l['geonameid'] . ')'. PHP_EOL;
}
*/

// Loop for admin1
/*
foreach( $raw_list as $l ) {
    $geojson = json_decode( $l['reverse_mapbox_response'], true );

    // set place geojson
    $i = false;
    foreach ( $geojson['features'] as $index => $feature ) {
        if ( array_search( 'region', $feature['place_type'] ) !== false ) {
            $i = $index;
            $geojson = $geojson['features'][$index];
        }
    }
    if ( $i === false ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') No "Region" features found.' . PHP_EOL;
        continue;
    }

    // record level is admin1
    if ( $l['level'] !== 'admin1' ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Level" not equal to admin1.' . PHP_EOL;
        continue;
    }

    // if context has id.region and id.country
    if ( substr( $geojson['context'][0]['id'],0, 7) !== 'country' ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Country" not found in context.' . PHP_EOL;
        continue;
    }

    // relevance is 1
    if ( $geojson['relevance'] !== 1 ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Relevance" is not equal to 1.' . PHP_EOL;
        continue;
    }

    // extract bbox and install to nswe
    // 0 = south
    // 1 = west
    // 2 = north
    // 3 = east

    if ( ! isset( $geojson['properties']['wikidata'] ) ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "No Wikidata.' . PHP_EOL;
        continue;
    }
    $wikidata = $geojson['properties']['wikidata'];

    $update = mysqli_query( $con, "UPDATE sg_missing_polygons_wiki SET
         wikidata='{$wikidata}'
        WHERE geonameid = {$l['geonameid']};");

    print date('H:i:s') . ' | Finished (' . $l['geonameid'] . ')'. PHP_EOL;
}
*/


// for Districts in Russia

foreach( $raw_list as $l ) {
    $geojson = json_decode( $l['reverse_mapbox_response'], true );

    // set place geojson
    $i = false;
    foreach ( $geojson['features'] as $index => $feature ) {
        if ( substr( $feature['id'],0, 8) === 'district') {
            $i = $index;
            $geojson = $geojson['features'][$index];
        }
    }
    if ( $i === false ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') No "District" features found.' . PHP_EOL;
        continue;
    }

    if ( $l['country_code'] !== 'RU' ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Not Russia.' . PHP_EOL;
        continue;
    }

    // relevance is 1
    if ( $geojson['relevance'] !== 1 ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "Relevance" is not equal to 1.' . PHP_EOL;
        continue;
    }

    // extract bbox and install to nswe
    // 0 = south
    // 1 = west
    // 2 = north
    // 3 = east

   if ( ! isset( $geojson['properties']['wikidata'] ) ) {
        print date('H:i:s') . ' | Skip (' . $l['geonameid'] . ') "No Wikidata.' . PHP_EOL;
        continue;
    }
    $wikidata = $geojson['properties']['wikidata'];

    $update = mysqli_query( $con, "UPDATE sg_missing_polygons_wiki SET
         wikidata='{$wikidata}'
        WHERE geonameid = {$l['geonameid']};");

    print date('H:i:s') . ' | Finished (' . $l['geonameid'] . ')'. PHP_EOL;
}


print date('H:i:s') . ' | End' . PHP_EOL;

mysqli_close($con);