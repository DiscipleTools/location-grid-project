<?php
/**
 * This script is intended to build one top level map at a time. It requires adding an array of two digit country codes.
 * And defining a file name to create.
 *
 */



/**
 * Step 1: Add and array of the two character country codes you want to make the grouping with. Like "US"
 */
$continent = [
"BJ",
"BF",
"CV",
"CI",
"GM",
"GH",
"GN",
"GW",
"LR",
"ML",
"MR",
"NE",
"NG",
"SH",
"SN",
"SL",
"TG",
];


/**
 * Step 2: Change this to the file name you want to create
 */
$new_file = 'test.geojson';







/********************************************************************************************************************
 * Leave below alone
 * run php
 */
$world_file = 'top_level_maps/world.geojson';
foreach( $continent as $index => $content ) {
    $continent[$index] = trim( strtoupper($content) );
}

// open world file
$file1 = fopen($world_file, "r");
$world = fread($file1,filesize($world_file));
fclose($file1);

// json decode world file
$world = json_decode( $world, true );

// loop through world file features
$i = 0;
$html = '{"type":"FeatureCollection","features":[';
foreach ( $world['features'] as $index => $country ) {

    $id = $country[ 'properties' ][ 'id' ];
    if ( ! array_search( $id, $continent ) ) {
        unset( $world[ 'features' ][ $index ] );
    } else {
        if ( 0 != $i ) {
            $html .= ',';
        }
        $html .= '{"type": "Feature","geometry": ';
        $html .= json_encode( $country['geometry'] );

        $html .= ',"properties":{';
        $html .= '"name":"' . $country['properties']['name'] . '",';
        $html .= '"id":"' . $country['id'] . '",';
        $html .= '"geonameid":' . $country['properties']['geonameid'];
        $html .= '}';
        $html .= ',"id":"' . $country['id'] .'"';

        $html .= '}';
        $i++;
    }
}
$html .= ']}';

 // make file
file_put_contents( 'top_level_maps/' . $new_file, $html.PHP_EOL );

print "Cool. Looks like that worked. Check for '" . $new_file . "' in the 'top_level_maps' folder." . PHP_EOL;

