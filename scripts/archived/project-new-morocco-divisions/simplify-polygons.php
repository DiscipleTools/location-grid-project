<?php
/**
 * Command line script
 * Gets geojson files from output and simplifies them using mapshaper cli and outputs them to simplified_output folder.
 * @version 1
 *
 */
// Simplify polygons with mapshaper
// @link https://github.com/mbloch/mapshaper

print date('H:i:s') . ' | Start ' . PHP_EOL;

$target_directory = getcwd() . '/output/'; // polygons single

$new_directory = $target_directory . 'simplified_output/';
if( ! is_dir( $new_directory ) ) {
    mkdir( $new_directory, 0755, true);
}


// scan target dir
$files = [];
$scan = scandir( $target_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
        $files[] = $file;
    }
}
//print_r( $files );

// scan new dir
$current_files = [];
$scan = scandir( $new_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
        $current_files[] = $file;
    }
}


foreach( $files as $file ) {
    if ( array_search( $file, $current_files ) === false ) {
        shell_exec('mapshaper '. $target_directory . $file .' -simplify '.$argv[1].'% -o '.$new_directory . $file.' -clean allow-empty');
        print date('H:i:s') . ' | ' . $file . PHP_EOL;
    }
}


$scan = scandir( $new_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
        $geojson = file_get_contents($new_directory . $file);
        $geojson = trim(preg_replace('/\n/', '', $geojson));
        file_put_contents( $new_directory . $file, $geojson );
    }
}
//print_r( $files );


print date('H:i:s') . ' | End ' . PHP_EOL;