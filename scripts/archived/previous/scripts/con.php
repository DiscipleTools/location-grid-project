<?php
// Extend PHP limits for large processing
ini_set('memory_limit', '5000M');

// define table names
$tables = [
    'grid' => 'location_grid',
    'grid_geometry' => 'location_grid_geometry',
];

// define live folders
$folders = [
    'root' =>  '../',
    'high' => '../high/',
    'low' => '../low/',
    'docs' => '../docs/',
    'docs_csv' => '../docs/csv/',
];

$output = [
    'output' => getcwd() . '/output/',
];
foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

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
