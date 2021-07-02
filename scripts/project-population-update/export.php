<?php

require_once( 'con.php' );

$output = [
    'lg' => getcwd() . '/files/',
    'root' => getcwd() . '/',
];

foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

$loop_count = 13;
$offset = 0;
for ($i = 0; $i <= $loop_count; $i++) {
    if ( file_exists( $output['lg'] . 'dt_full_location_grid_'.$i.'.tsv' ) ) {
        unlink($output['lg'] . 'dt_full_location_grid_'.$i.'.tsv');
    }
    $results = mysqli_query( $con, "
        SELECT * FROM location_grid LIMIT 30000 OFFSET {$offset} INTO OUTFILE '{$output['lg']}dt_full_location_grid_{$i}.tsv'
        FIELDS TERMINATED BY '\t'
        LINES TERMINATED BY '\n';
    " );
    print 'offset '. $offset . PHP_EOL;
    if ( filesize( $output['lg'] . 'dt_full_location_grid_'.$i.'.tsv' ) === 0 ) {
        unlink( $output['lg'] . 'dt_full_location_grid_'.$i.'.tsv' );
        print date('H:i:s') . ' | ' . 'dt_full_location_grid_'.$i.'.tsv no value. Removed.'. PHP_EOL;
    }

    $offset = $offset + 30000;
}