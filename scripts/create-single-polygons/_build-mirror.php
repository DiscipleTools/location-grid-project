<?php
/**
 * Create all files in a country.
 * $ php {filename} {grid_id} // defaults to admin0 query
 * $ php {filename} {grid_id} {true} // queries by parent_id
 */
require_once( 'con.php' );

$testing = false; // change this to false when ready to run for production.

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
    'output' => '/Users/chris/Documents/LOCATION-GRID-MIRROR/v2.transition/output-single/',
    'low' => '/Users/chris/Documents/LOCATION-GRID-MIRROR/v2.transition/low/',
    'high' => '/Users/chris/Documents/LOCATION-GRID-MIRROR/v2.transition/high/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}


/********************************************************************************************************************
 *
 * BUILD RAW GEOJSON FILES
 *
 ********************************************************************************************************************/
print 'QUERY LIST' . PHP_EOL;
$list_raw = mysqli_query( $con,
    "SELECT lg.grid_id
                    FROM location_grid as lg;" );

if ( empty( $list_raw ) ) {
    print_r( $con );
    die();
}
$list = mysqli_fetch_all( $list_raw, MYSQLI_ASSOC );
$list = array_map(function ( $a ) { return $a['grid_id'];}, $list );

$test = 0;

print '***************************************************************************************************' . PHP_EOL;
print 'BEGIN LOOP LIST CREATION' . PHP_EOL;
print '***************************************************************************************************' . PHP_EOL;
$pi = 0;
foreach( $list as $id ) {
    $pi++;
    if ( substr($pi, -2, 2) == '00' ){
        print $pi . '...'. PHP_EOL;
    }

    $query_raw = mysqli_query( $con,
        "SELECT 
                    lg.*, 
                    g.geoJSON, 
                    a0.name as admin0_name,
                    a1.name as admin1_name,
                    a2.name as admin2_name,
                    a3.name as admin3_name,
                    a4.name as admin4_name,
                    a5.name as admin5_name
                    FROM location_grid as lg 
                    LEFT JOIN location_grid_geometry as g ON g.grid_id=lg.grid_id 
                    LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
                    LEFT JOIN location_grid as a1 ON lg.admin1_grid_id=a1.grid_id
                    LEFT JOIN location_grid as a2 ON lg.admin2_grid_id=a2.grid_id
                    LEFT JOIN location_grid as a3 ON lg.admin3_grid_id=a3.grid_id
                    LEFT JOIN location_grid as a4 ON lg.admin4_grid_id=a4.grid_id
                    LEFT JOIN location_grid as a5 ON lg.admin5_grid_id=a5.grid_id
                    WHERE lg.grid_id = '{$id}'
                    " );

    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $result = mysqli_fetch_assoc( $query_raw );

    /* Feature collection */
    $features = [];
    $geometry = $result['geoJSON'];
    if ( empty( $geometry ) ) {
        continue;
    }

    $features[] = array(
        "type" => "Feature",
        'id' => $result['grid_id'],
        "properties" => array(
            "grid_id" => (int) $result['grid_id'],
            'full_name' => _full_name($result),
        ),
        "geometry" => json_decode( $geometry, true ),
    );
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

    file_put_contents( $output['output'] . $result['grid_id'] .  '.geojson', $geojson );


    // limit run for testing
    if ( $testing ) {
        $test++;
        if ( $test > 1001){
            break;
        }
    }
}

print PHP_EOL . 'END MIRROR BUILD' . PHP_EOL . PHP_EOL;


/********************************************************************************************************************
 *
 * MOVE AND SIMPLIFY 50%
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
                if ( filesize($output['high'] . $file) > $max_high_size * 1000  ) {
                    shell_exec('rm '. $output['high'] . $file );
                }
                else {
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

        // Low
        if ( array_search( $file, $current_low_files ) === false ){
            if ( filesize( $output['output'] . $file) < $acceptable_low_size * 1000 ) {
                shell_exec('cp '. $output['output'] . $file .' '.$output['low'] . $file );
                shell_exec('rm '. $output['output'] . $file );
                print date('H:i:s') . ' Moved Low | ' . $file . PHP_EOL;
            }
            else {
                shell_exec('mapshaper '. $output['output'] . $file .' -simplify dp keep-shapes '.$percent_reduction.'% -o '.$output['low'] . $file.' -clean allow-empty');
                if ( filesize($output['low'] . $file) > $max_low_size * 1000  ) {
                    shell_exec('rm '. $output['low'] . $file );
                }
                else {
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
        } else {
            shell_exec('rm '. $output['output'] . $file );
        }

    }
}

print '**********************************************************************************************************' . PHP_EOL;
print '                                             FINISH' . PHP_EOL;
print '**********************************************************************************************************' . PHP_EOL;

