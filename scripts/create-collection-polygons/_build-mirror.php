<?php
/**
 * Create all files in a country.
 * $ php {filename} {grid_id} // defaults to admin0 query
 * $ php {filename} {grid_id} {true} // queries by parent_id
 */
require_once( 'con.php' );

$testing = true; // change this to false when ready to run for production.

print '***************************************************************************************************' . PHP_EOL;
print 'BEGIN BUILD MIRROR' . PHP_EOL;
print '***************************************************************************************************' . PHP_EOL;

/********************************************************************************************************************
 *
 * SETUP PROCESSING AND DESTINATION FOLDERS
 *
 ********************************************************************************************************************/
print 'BUILD DIRECTORIES' . PHP_EOL;
$output = [
    'output' => '/Users/chris/Documents/Projects/location-grid-render/output-combined/',
    'combined' => '/Users/chris/Documents/Projects/location-grid-render/combined/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}


$list_raw = mysqli_query( $con,
    "SELECT DISTINCT lg.parent_id
                FROM location_grid lg
                WHERE lg.parent_id IS NOT NULL" );
if ( empty( $list_raw ) ) {
    print_r( $con );
    die();
}
$list = mysqli_fetch_all( $list_raw, MYSQLI_ASSOC );
$list = array_map(function ( $a ) { return $a['parent_id'];}, $list );

$test = 0;
$pi = 0;
foreach( $list as $parent_id ) {
    $pi++;
    if ( substr($pi, -2, 2) == '00' ){
        print $pi . '...'. PHP_EOL;
    }

    $query_raw = mysqli_query( $con,
        "SELECT lg.*, lgg.geoJSON
            FROM location_grid lg
            LEFT JOIN location_grid_geometry lgg ON lgg.grid_id=lg.grid_id
            WHERE lg.parent_id = {$parent_id}" );

    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

    /* Feature collection */
    $features = [];
    foreach( $query as $result ) {

        $grid_id = $result['grid_id'];
        $geometry = $result['geoJSON'];

        $features[] = array(
            "type" => "Feature",
            'id' => $result['grid_id'],
            "properties" => array(
                "grid_id" => (int) $result['grid_id'],
                'full_name' => _full_name($result),
            ),
            "geometry" => json_decode( $geometry, true ),
        );
    }

    $geojson = array(
        'type' => "FeatureCollection",
        'features' => $features,
    );
    $geojson = json_encode( $geojson );
    $geojson = trim(preg_replace('/\n/', '', $geojson));
    $geojson = trim(preg_replace('/, "/', ',"', $geojson));
    $geojson = trim(preg_replace('/: "/', ':"', $geojson));
    $geojson = trim(preg_replace('/: \[/', ':[', $geojson));
    $geojson = trim(preg_replace('/: {/', ':{', $geojson));

    file_put_contents( $output['output'] . $parent_id .  '.geojson', $geojson );

    // limit run for testing
    if ( $testing ) {
        $test++;
        if ( $test > 1001){
            break;
        }
    }

}


/********************************************************************************************************************
 *
 * MOVE AND SIMPLIFY
 *
 ********************************************************************************************************************/
print '***************************************************************************************************' . PHP_EOL;
print PHP_EOL . 'BEGIN SIMPLIFY BUILD' . PHP_EOL;
print '***************************************************************************************************' . PHP_EOL;
$reductions = [
    50 => [
        'percent_reduction' => 50,
        'max_low_size' => 100,
        'max_high_size' => 200,
        'acceptable_low_size' => 100,
        'acceptable_high_size' => 200,
    ],
    30 => [
        'percent_reduction' => 30,
        'max_low_size' => 100,
        'max_high_size' => 300,
        'acceptable_low_size' => 100,
        'acceptable_high_size' => 300,
    ],
    10 => [
        'percent_reduction' => 10,
        'max_low_size' => 150,
        'max_high_size' => 400,
        'acceptable_low_size' => 150,
        'acceptable_high_size' => 400,
    ],
    1 => [
        'percent_reduction' => 1,
        'max_low_size' => 300,
        'max_high_size' => 700,
        'acceptable_low_size' => 300,
        'acceptable_high_size' => 700,
    ],
];

foreach( $reductions as $setting ) {
    $max_low_size = $setting['max_low_size'];
    $max_high_size = $setting['max_high_size'];
    $acceptable_low_size = $setting['acceptable_low_size'];
    $acceptable_high_size = $setting['acceptable_high_size'];
    $percent_reduction = $setting['percent_reduction'];

    print '***************************************************************************************************' . PHP_EOL;
    print PHP_EOL . 'BEGIN SIMPLIFY BUILD - ' . $setting['percent_reduction'] . PHP_EOL;
    print '***************************************************************************************************' . PHP_EOL;


    // scan output
    print date('H:i:s') . ' | Collect Output Dir : ';
    $files = [];
    $scan = scandir( $output['output'] );
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            $files[] = $file;
        }
    }
    print count($files) . PHP_EOL;

    // scan high
    print date('H:i:s') . ' | Collect High Dir : ';
    $current_high_files = [];
    $scan = scandir( $output['low'] );
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            $current_high_files[] = $file;
        }
    }
    if ( ! is_array( $current_high_files) ){
        $current_high_files = [];
    }
    print count($current_high_files) . PHP_EOL;


    // scan low
    print date('H:i:s') . ' | Collect Low Dir : ';
    $current_low_files = [];
    $scan = scandir( $output['low'] );
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            $current_low_files[] = $file;
        }
    }
    if ( ! is_array( $current_low_files) ){
        $current_low_files = [];
    }
    print count($current_low_files) . PHP_EOL;

    print '***************************************************************************************************' . PHP_EOL;
    print date('H:i:s') . ' | SIMPLIFY ' . PHP_EOL;
    // move or simplify
    $pi = 0;
    foreach( $files as $file ) {
        $pi++;
        if ( substr($pi, -3, 3) == '00' ){
            print $pi . ' | ***************************************************************************************************' . PHP_EOL;
        }

        // High
        if ( array_search( $file, $current_high_files ) === false ){
            if ( filesize( $output['output'] . $file) < $acceptable_high_size * 1000 ) {
                shell_exec('cp '. $output['output'] . $file .' '.$output['high'] . $file );
                print date('H:i:s') . ' Moved High | ' . $file . PHP_EOL;
            }
            else {
                shell_exec('mapshaper '. $output['output'] . $file .' -simplify dp keep-shapes '.$percent_reduction.'% -o '.$output['high'] . $file.' -clean allow-empty');
            }
        }

        // Low
        if ( array_search( $file, $current_low_files ) === false ){
            if ( filesize( $output['output'] . $file) < $acceptable_low_size * 1000 ) {
                shell_exec('cp '. $output['output'] . $file .' '.$output['low'] . $file );
                shell_exec('rm '. $output['output'] . $file );
                print date('H:i:s') . ' Moved Low | ' . $file . PHP_EOL;
            }
            else {
                shell_exec('mapshaper '. $output['output'] . $file .' -simplify dp keep-shapes '.$percent_reduction.'% -o '.$output['low'] . $file.' -clean allow-empty');
            }
        } else {
            shell_exec('rm '. $output['output'] . $file );
        }

    }

    print '***************************************************************************************************' . PHP_EOL;
    print date('H:i:s') . ' | DELETE LARGE FILES ' . PHP_EOL;
    if ( $setting['percent_reduction'] !== 1 ) { // don't delete on the last pass
        // delete large high
        $current_files = [];
        $scan = scandir( $output['high']  );
        $folder = $output['high'];
        foreach( $scan as $file ) {
            if ( preg_match( '/.geojson/', $file ) ) {
                shell_exec("find $folder -name '*.geojson' -type 'f' -size +".$max_high_size."k -delete");
            }
        }

        // delete large low
        $current_files = [];
        $scan = scandir( $output['low']  );
        $folder = $output['low'];
        foreach( $scan as $file ) {
            if ( preg_match( '/.geojson/', $file ) ) {
                shell_exec("find $folder -name '*.geojson' -type 'f' -size +".$max_low_size."k -delete");
            }
        }
    }


    // quality check on high
    print '***************************************************************************************************' . PHP_EOL;
    print date('H:i:s') . ' | Check Quality High ' . PHP_EOL;
    $scan = scandir( $output['high'] );
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            if ( filesize($output['high'] . $file) < 10000000 ) {
                $geojson = file_get_contents($output['high'] . $file);
                $geojson = trim(preg_replace('/\n/', '', $geojson));
                $geojson = trim(preg_replace('/, "/', ',"', $geojson));
                $geojson = trim(preg_replace('/: "/', ':"', $geojson));
                $geojson = trim(preg_replace('/: \[/', ':[', $geojson));
                $geojson = trim(preg_replace('/: {/', ':{', $geojson));
                file_put_contents( $output['high'] . $file, $geojson );
                $geojson = null;
            }
        }
    }

    // quality check on low
    print date('H:i:s') . ' | Check Quality Low ' . PHP_EOL;
    $scan = scandir( $output['low'] );
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            if ( filesize($output['low'] . $file) < 10000000 ) {
                $geojson = file_get_contents($output['low'] . $file);
                $geojson = trim(preg_replace('/\n/', '', $geojson));
                $geojson = trim(preg_replace('/, "/', ',"', $geojson));
                $geojson = trim(preg_replace('/: "/', ':"', $geojson));
                $geojson = trim(preg_replace('/: \[/', ':[', $geojson));
                $geojson = trim(preg_replace('/: {/', ':{', $geojson));
                file_put_contents( $output['low'] . $file, $geojson );
                $geojson = null;
            }
        }
    }
}

print '**********************************************************************************************************' . PHP_EOL;
print '                                             FINISH' . PHP_EOL;
print '**********************************************************************************************************' . PHP_EOL;

