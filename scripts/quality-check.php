<?php
/**
 * Check if all expected files are present
 * $ php quality-check.php -present
 *
 * Check that no files are less than 50bytes
 * $
 */

require( 'con.php' );

print date('H:i:s') . ' | START ' . PHP_EOL;

if ( ! isset( $argv[1] ) ) {
   print 'Quality check not selected. Missing parameter (present, notzero, ...)' . PHP_EOL;
   die();
}

$check_directory_root = '/Users/chris/Documents/LOCATION-GRID-MIRROR/v2.master/location-grid-mirror-v2/';
$lg = 'location_grid'; // grid table
$lgg = 'location_grid_geometry'; // grid geometry table

if ( 'json_db' === $argv[1]  ){

    // get full grid list
    $query_raw = mysqli_query( $con,
        "SELECT 
                lg.grid_id
                FROM {$lg} as lg              
                " );

    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );
    $list = array_map(function ( $a ) { return $a['grid_id'];}, $query );

    /**
     * Scan base folder
     */
    $target_dir = $check_directory_root  . 'json_db/';
    if ( ! is_dir( $target_dir ) ){
        print 'Could not find directory' . PHP_EOL;
        die();
    }
    $scan = scandir( $target_dir );
    $files = [];
    foreach( $scan as $file ) {
        if ( preg_match( '/.json/', $file ) ) {
            $id = trim( str_replace('.json', '', $file ) );
            $files[$id] = $file;
        }
    }

    $i = 0;
    $s = 0;
    foreach( $list as $grid_id ) {
        if ( ! isset( $files[$grid_id] ) ) {
            print $grid_id . ' - missing '. PHP_EOL;
            $i++;
        }
        else if ( filesize($target_dir . $files[$grid_id]) < 50 ) {
            print $grid_id . ' - too small '. PHP_EOL;
            $s++;
        }
    }

    print 'Base Files Missing : ' . $i . PHP_EOL;
    print 'Base Files Too Small: ' . $s . PHP_EOL;

    /**
     * Scan children folder
     */
    $target_dir = $check_directory_root  . 'json_db/children/';
    if ( ! is_dir( $target_dir ) ){
        print 'Could not find directory' . PHP_EOL;
        die();
    }
    $scan = scandir( $target_dir );
    $files = [];
    foreach( $scan as $file ) {
        if ( preg_match( '/.json/', $file ) ) {
            $id = trim( str_replace('.json', '', $file ) );
            $files[$id] = $file;
        }
    }

    $i = 0;
    $s = 0;
    foreach( $list as $grid_id ) {
        if ( ! isset( $files[$grid_id] ) ) {
            print $grid_id . ' - missing '. PHP_EOL;
            $i++;
        }
        else if ( filesize($target_dir . $files[$grid_id]) < 50 ) {
            print $grid_id . ' - too small '. PHP_EOL;
            $s++;
        }
    }

    print 'Children Files Missing : ' . $i . PHP_EOL;
    print 'Children Files Too Small: ' . $s . PHP_EOL;


}
else if ( 'low' === $argv[1] || 'high' === $argv[1] ) {
    // get full grid list
    $query_raw = mysqli_query( $con,
        "SELECT 
                lg.grid_id
                FROM {$lg} as lg              
                " );

    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );
    $list = array_map(function ( $a ) { return $a['grid_id'];}, $query );

    if ( 'low' === $argv[1] ) {
        $target_dir = $check_directory_root  . 'low/';
    } else {
        $target_dir = $check_directory_root  . 'high/';
    }
    if ( ! is_dir( $target_dir ) ){
        print 'Could not find directory' . PHP_EOL;
        die();
    }
    $scan = scandir( $target_dir );
    $files = [];
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            $id = trim( str_replace('.geojson', '', $file ) );
            $files[$id] = $file;
        }
    }

    $i = 0;
    $s = 0;
    foreach( $list as $grid_id ) {
        if ( ! isset( $files[$grid_id] ) ) {
            print $grid_id . ' - missing '. PHP_EOL;
            $i++;

            mysqli_query( $con, "INSERT INTO quality_check (grid_id) VALUES ($grid_id)");

        }
        else if ( filesize($target_dir . $files[$grid_id]) < 50 ) {
            print $grid_id . ' - too small '. PHP_EOL;
            $s++;
        }
    }

    print 'Files Missing : ' . $i . PHP_EOL;
    print 'Files Too Small: ' . $s . PHP_EOL;

}
else if ( 'collection' === $argv[1] ){
    $query_raw = mysqli_query( $con,
        "SELECT DISTINCT lg.parent_id
                FROM {$lg} lg
                WHERE lg.parent_id IS NOT NULL             
                " );

    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );
    $list = array_map(function ( $a ) { return $a['parent_id'];}, $query );

    $target_dir = $check_directory_root  . 'collection/';
    if ( ! is_dir( $target_dir ) ){
        print 'Could not find directory' . PHP_EOL;
        die();
    }
    $scan = scandir( $target_dir );
    $files = [];
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            $id = trim( str_replace('.geojson', '', $file ) );
            $files[$id] = $file;
        }
    }

    $i = 0;
    $s = 0;
    foreach( $list as $grid_id ) {

        if ( ! isset( $files[$grid_id] ) ) {
            print $grid_id . ' - missing '. PHP_EOL;
            $i++;
            mysqli_query( $con, "INSERT INTO quality_check (grid_id) VALUES ($grid_id)");
        }
        else if ( filesize($target_dir . $files[$grid_id]) < 50 ) {
            print $grid_id . ' - too small '. PHP_EOL;
            $s++;
        }
    }

    print 'Files Missing : ' . $i . PHP_EOL;
    print 'Files Too Small: ' . $s . PHP_EOL;

}
else if ( 'nullcheck' === $argv[1] ) {
    if ( ! isset( $argv[2] ) ) {
        print 'Second argument not placed.' . PHP_EOL;
    }
    switch($argv[2] ) {
        case 'collection':
            $target_dir = $check_directory_root  . 'collection/';
            break;
        case 'low':
            $target_dir = $check_directory_root  . 'low/';
            break;
        case 'high':
            $target_dir = $check_directory_root  . 'high/';
            break;
    }

    if ( ! is_dir( $target_dir ) ){
        print 'Could not find directory' . PHP_EOL;
        die();
    }
    $scan = scandir( $target_dir );
    $files = [];
    $i = 0;
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            print $file;
            $geojson = json_decode( file_get_contents($target_dir . $file), true );
            foreach( $geojson['features'] as $index => $feature ) {
                if ( is_null( $feature['geometry'] ) ) {
                    $i++;
                    print $file . ': ' . $index. PHP_EOL;
                    die();
                }
                else {
                    print '.';
                }

            }
            print PHP_EOL;
            $geojson = null;
        }
    }

    print 'Nulls found : ' . $i . PHP_EOL;
}
else {
    print 'No quality test found for parameter.' . PHP_EOL;
}







print date('H:i:s') . ' | END ' . PHP_EOL;