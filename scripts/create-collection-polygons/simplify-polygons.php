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

if ( isset( $argv[2] ) && is_numeric( $argv[2] ) ) {
    $target_directory = getcwd() . '/output/' . $argv[2] . '/';
    $new_directory = getcwd() . '/output/simplified_output/' . $argv[2] . '/';
} else {
    $target_directory = getcwd() . '/output/'; // polygons single
    $new_directory = $target_directory . 'simplified_output/';
}
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
        if ( filesize($new_directory . $file) < 10000000 ) {
            $geojson = file_get_contents($new_directory . $file);
            $geojson = trim(preg_replace('/\n/', '', $geojson));
            $geojson = trim(preg_replace('/, "/', ',"', $geojson));
            $geojson = trim(preg_replace('/: "/', ':"', $geojson));
            $geojson = trim(preg_replace('/: \[/', ':[', $geojson));
            $geojson = trim(preg_replace('/: {/', ':{', $geojson));
            file_put_contents( $new_directory . $file, $geojson );
            $geojson = null;
        }
    }
}

print date('H:i:s') . ' | End ' . PHP_EOL;
