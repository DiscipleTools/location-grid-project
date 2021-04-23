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
    'output' => '/Users/chris/Documents/Projects/location-grid-render/output-combined/',
    'target' => '/Users/chris/Documents/Projects/location-grid-render/combined/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

print 'GET LIST' . PHP_EOL;
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


print '***************************************************************************************************' . PHP_EOL;
print 'LOOP LIST' . PHP_EOL;
$test = 0;
$pi = 0;
foreach( $list as $parent_id ) {
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
        'id' => (int) $parent_id,
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
        'max_size' => 100,
        'acceptable_size' => 100,
    ],
    30 => [
        'percent_reduction' => 30,
        'max_size' => 100,
        'acceptable_size' => 100,
    ],
    10 => [
        'percent_reduction' => 10,
        'max_size' => 150,
        'acceptable_size' => 150,
    ],
    1 => [
        'percent_reduction' => 1,
        'max_size' => 300,
        'acceptable_size' => 300,
    ],
    05 => [
        'percent_reduction' => 0.5,
        'max_size' => 500,
        'acceptable_size' => 400,
    ],
];

foreach( $reductions as $setting ) {
    $max_size = $setting['max_size'];
    $acceptable_size = $setting['acceptable_size'];
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
    

    // scan combined
    print date('H:i:s') . ' | Collect Low Dir : ';
    $current_combined_files = [];
    $scan = scandir( $output['target'] );
    foreach( $scan as $file ) {
        if ( preg_match( '/.geojson/', $file ) ) {
            $current_combined_files[] = $file;
        }
    }
    if ( ! is_array( $current_combined_files) ){
        $current_combined_files = [];
    }
    print count($current_combined_files) . PHP_EOL;

    print '***************************************************************************************************' . PHP_EOL;
    print date('H:i:s') . ' | SIMPLIFY ' . PHP_EOL;
    // move or simplify
    $files_to_review = [];
    $pi = 0;
    foreach( $files as $file ) {
        $files_to_review[] = $file;
        $pi++;
        if ( substr($pi, -3, 3) == '00' ){
            print $pi . ' | ***************************************************************************************************' . PHP_EOL;
            print 'reduction'.PHP_EOL;
            if ( $setting['percent_reduction'] !== 0.5 ) { // don't delete on the last pass
                // delete large combined
                $scan = $files_to_review;
                $folder = $output['target'];
                foreach( $scan as $scan_file ) {
                    if ( filesize($output['target'] . $scan_file) > $max_size * 1000  ) {
                        shell_exec('rm '. $output['target'] . $scan_file );
                    }
                }
            }
            print 'quality check'.PHP_EOL;
            $scan = $files_to_review;
            foreach( $scan as $quality_file ) {
                if ( preg_match( '/.geojson/', $quality_file ) ) {
                    if ( filesize($output['target'] . $quality_file) < 10000000 ) {
                        $geojson = file_get_contents($output['target'] . $quality_file);
                        $geojson = trim(preg_replace('/\n/', '', $geojson));
                        $geojson = trim(preg_replace('/, "/', ',"', $geojson));
                        $geojson = trim(preg_replace('/: "/', ':"', $geojson));
                        $geojson = trim(preg_replace('/: \[/', ':[', $geojson));
                        $geojson = trim(preg_replace('/: {/', ':{', $geojson));
                        file_put_contents( $output['target'] . $quality_file, $geojson );
                        $geojson = null;
                    }
                }
            }
            $files_to_review = [];
            $files_to_review[] = $file;
        }
        
        // Low
        if ( array_search( $file, $current_combined_files ) === false ){
            if ( filesize( $output['output'] . $file) < $acceptable_size * 1000 ) {
                shell_exec('cp '. $output['output'] . $file .' '.$output['target'] . $file );
                shell_exec('rm '. $output['output'] . $file );
                print date('H:i:s') . ' Moved | ' . $file . PHP_EOL;
            }
            else {
                shell_exec('mapshaper '. $output['output'] . $file .' -simplify dp keep-shapes '.$percent_reduction.'% -o '.$output['target'] . $file.' -clean allow-empty');
            }
        } else {
            shell_exec('rm '. $output['output'] . $file );
        }

    }

}

print '**********************************************************************************************************' . PHP_EOL;
print '                                             FINISH' . PHP_EOL;
print '**********************************************************************************************************' . PHP_EOL;

