<?php
/**
 * Command line script
 * Gets geojson files from output and simplifies them using mapshaper cli and outputs them to simplified_output folder.
 * @version 1
 *
 */
// Simplify polygons with mapshaper
// @link https://github.com/mbloch/mapshaper

require_once( 'con.php' );
ini_set('memory_limit', '50000000M');

print date('H:i:s') . ' | Start ' . PHP_EOL;

$new_directory = getcwd() . '/output/simplified_output/';
$i = 0;
$scan = scandir( $new_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
            $geojson = json_decode( file_get_contents($new_directory . $file), true );

            foreach( $geojson['features'] as $feature ) {
                $i++;
                if ( is_null( $feature['geometry'] ) ) {
                    print '********************************************: ' . PHP_EOL . $file . PHP_EOL;
                    print $feature['properties']['full_name'] . ' | ' . $feature['properties']['grid_id'] . PHP_EOL;

                    $query_raw = mysqli_query( $con,
                        "SELECT lgg.geoJSON
                            FROM location_grid_geometry lgg
                            WHERE lgg.grid_id = {$feature['id']}" );
                    if ( empty( $query_raw ) ) {
                        print_r( $con );
                        continue;
                    }
                    $query = mysqli_fetch_assoc( $query_raw );
                    print 'geoJSON length: ' . strlen( $query['geoJSON'] ) . PHP_EOL;
                }
            }

            $geojson = null;

        }
}
print 'Checked features: ' . $i . PHP_EOL;
print date('H:i:s') . ' | End ' . PHP_EOL;
