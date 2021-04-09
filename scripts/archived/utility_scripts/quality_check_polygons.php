<?php

require_once( 'con.php' );
unset( $folders['root'] );

print date('H:i:s') . ' | Begin ' . PHP_EOL;

foreach ( $folders as $name => $folder ) {

    ob_start();

    $dir_raw = scandir( $folder );
    foreach ( $dir_raw as $file_name ) {
        if ( substr( $file_name, - 7 ) === 'geojson' ) {
            $file = json_decode( file_get_contents( $folder . $file_name ), true );
            if ( empty( $file ) ) {
                print date( 'H:i:s' ) . ' | (' . $file_name . ') Completely Empty File. ' . PHP_EOL;
                continue;
            }

            $missing = 0;
            foreach ( $file[ 'features' ] as $feature ) {
                if ( empty( $feature[ 'geometry' ] ) ) {
                    $missing ++;
                }
            }

            if ( $missing ) {
                print date( 'H:i:s' ) . ' | (' . $file_name . ') Missing ' . $missing . ' fields. ' . PHP_EOL;
            }
        }
    }

    $content = ob_get_contents();

    file_put_contents( $output[ 'polygon_collection' ] . 'quality_check_'.$name.'.txt', $content );

    ob_end_clean();

}

print date('H:i:s') . ' | Finish ' . PHP_EOL;