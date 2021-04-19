<?php
/**
 * Command Line
 *
 * $ php import-geojson.php {start_increment_number} {table_name} {country_code} {time_zone}
 */

require_once 'con.php';

$content = [];
$dir = scandir('1-raw-source' );
if ( empty( $dir ) ) {
    die('No files');
}
foreach ( $dir as $i => $v ) {
    if ( substr( $v, -4, 4 ) !== 'json' ) {
        unset( $dir[$i] );
    }
}

foreach ( $dir as $file ) {
    print $file . PHP_EOL;

    $geojson = json_decode( file_get_contents( '1-raw-source/' . $file ), true );
    if ( empty( $geojson ) ) {
        die( 'no content' );
    }

    $results = [];
    if ( ! empty( $geojson['features'] ) ) {
        foreach ( $geojson['features'] as $feature ) {

            $geoJSON = json_encode($feature['geometry']);

            $x = mysqli_query( $con, "
            INSERT INTO morocco_import
            (
              `ADM0_EN`,
              `ADM0_PCODE`,
              `ADM1_EN`,
              `ADM1_PCODE`,
              `ADM2_EN`,
              `ADM2_PCODE`,
              `geoJSON`
            )
            VALUES (
              '{$feature['properties']['ADM0_EN']}',
              '{$feature['properties']['ADM0_PCODE']}',
              '{$feature['properties']['ADM1_EN']}',
              '{$feature['properties']['ADM1_PCODE']}',
              '{$feature['properties']['ADM2_EN']}',
              '{$feature['properties']['ADM2_PCODE']}',
              '{$geoJSON}'
            )
        " );

        }
    }

}

//print_r($results);

print 'Finish' . PHP_EOL;

mysqli_close($con );

