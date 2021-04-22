<?php
/**
 * Command line script
 * Gets geojson files from output and simplifies them using mapshaper cli and outputs them to simplified_output folder.
 * @version 1
 *
 */
// Simplify polygons with mapshaper
// @link https://github.com/mbloch/mapshaper
// $ php simplify.php {percent to reduce} {min filesize kb to reduce} {max filesize kb to delete}
// min : determines if a file is small enough to not simplify
// max : determines if a file is too large and should be deleted


// php simplify.php 50 100 100

require( 'con.php' );

print date('H:i:s') . ' | Start ' . PHP_EOL;

$output_directory = '/Users/chris/Documents/output/'; // polygons single
if( ! is_dir( $output_directory ) ) {
    mkdir( $output_directory, 0755, true);
}
$simplified_directory = $output_directory . 'simplified_output/';
if( ! is_dir( $simplified_directory ) ) {
    mkdir( $simplified_directory, 0755, true);
}

$kb_min = 100;
if ( isset( $argv[2] ) ) {
    $kb_min = $argv[2];
}

$kb_max = 100;
if ( isset( $argv[3] ) ) {
    $kb_max = $argv[3];
}

print date('H:i:s') . ' | Collect Output Dir ' . PHP_EOL;
// scan target dir
$files = [];
$scan = scandir( $output_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
        $files[] = $file;
    }
}

print date('H:i:s') . ' | Collect New Dir ' . PHP_EOL;
// scan new dir
$current_files = [];
$scan = scandir( $simplified_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
        $current_files[] = $file;
    }
}

print date('H:i:s') . ' | Process Files ' . PHP_EOL;
print date('H:i:s') . ' | Raw Files ' . count($files) . PHP_EOL;
print date('H:i:s') . ' | Simplified Files ' . count($current_files) . PHP_EOL;

$i = 0;
foreach( $files as $file ) {
    $i++;
    if ( substr($i, -3, 3) == '000' ){
        print PHP_EOL . $i . PHP_EOL;
    }
    if ( array_search( $file, $current_files ) !== false ){
        print '*';
        continue;
    }
    if ( filesize( $output_directory . $file) < $kb_min * 1000 ) {
        shell_exec('cp '. $output_directory . $file .' '.$simplified_directory . $file );
        shell_exec('rm '. $output_directory . $file );
        print date('H:i:s') . 'Moved | ' . $file . PHP_EOL;
    }
    else {
        shell_exec('mapshaper '. $output_directory . $file .' -simplify dp keep-shapes '.$argv[1].'% -o '.$simplified_directory . $file.' -clean allow-empty');
        print date('H:i:s') . 'Simplified | ' . $file . PHP_EOL;
    }
}

print date('H:i:s') . ' | Check Quality ' . PHP_EOL;
$scan = scandir( $simplified_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
        if ( filesize($simplified_directory . $file) < 10000000 ) {
            $geojson = file_get_contents($simplified_directory . $file);
            $geojson = trim(preg_replace('/\n/', '', $geojson));
            $geojson = trim(preg_replace('/, "/', ',"', $geojson));
            $geojson = trim(preg_replace('/: "/', ':"', $geojson));
            $geojson = trim(preg_replace('/: \[/', ':[', $geojson));
            $geojson = trim(preg_replace('/: {/', ':{', $geojson));
            file_put_contents( $simplified_directory . $file, $geojson );
            $geojson = null;
        }
    }
}

print date('H:i:s') . ' | Remove Large ' . PHP_EOL;
// scan new dir
$current_files = [];
$scan = scandir( $simplified_directory );
foreach( $scan as $file ) {
    if ( preg_match( '/.geojson/', $file ) ) {
        shell_exec("find $simplified_directory -name '*.geojson' -type 'f' -size +".$kb_max."k -delete");
    }
}

print date('H:i:s') . ' | End ' . PHP_EOL;

// find . -name "*.tif" -type 'f' -size -160k -delete
