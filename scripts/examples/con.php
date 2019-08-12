<?php
// Extend PHP limits for large processing
ini_set('memory_limit', '50000M');

// define and create output directories
$output = [
    'output' => getcwd() . '/output/',
    'polygon' => getcwd() . '/output/polygon/',
    'polygon_collection' => getcwd() . '/output/polygon_collection/',
    'point_collection' => getcwd() . '/output/point_collection/',
    'boundaries' => getcwd() . '/output/boundaries/',
    'cities' => getcwd() . '/output/cities/',
    'census' => getcwd() . '/output/census/',
    'geojson_upload' => getcwd() . '/output/geojson_upload/',
    'import' => getcwd() . '/output/import/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

// define live folders
$folders = [
    'root' =>  '../saturation-grid-project/',
    'polygon' => '../saturation-grid-project/polygon/',
    'polygon_collection' => '../saturation-grid-project/polygon_collection/',
    'point_collection' => '../saturation-grid-project/point_collection/',
    'missing' => '../saturation-grid-project/missing/',
    'missing_csv' => '../saturation-grid-project/missing/csv/',
];

// define table names
$tables = [
    'geonames' => 'location_grid',
    'geonames_old' => 'saturation_grid_geonames',
    'polygons' => 'location_grid_geometry',
    'polygons_old' => 'saturation_grid_polygons',
    'boundaries' => 'dt_geonames_boundaries',
    'hierarchy' => 'geonames_hierarchy',
    'zipcodes' => 'geonames_zipcodes',
];



// define database connection
if ( ! file_exists( 'connect_params.json') ) {
    $content = '{"host": "","username": "","password": "","database": ""}';
    file_put_contents( 'connect_params.json', $content );
}
$params = json_decode( file_get_contents( "connect_params.json" ), true );
if ( empty( $params['host'] ) ) {
    print 'You have just created the connect_params.json file, but you still need to add database connection information.
Please, open the connect_params.json file and add host, username, password, and database information.' . PHP_EOL;
    die();
}
$con = mysqli_connect( $params['host'], $params['username'], $params['password'],$params['database']);
if (!$con) {
    echo 'mysqli Connection FAILED. Check parameters inside connect_params.json file.' . PHP_EOL;
    die();
}

