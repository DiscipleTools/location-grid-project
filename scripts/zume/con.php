<?php
// Extend PHP limits for large processing
ini_set('memory_limit', '50000M');

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
$con = mysqli_connect( $params['host'], $params['username'], $params['password'], $params['database']);
if (!$con) {
    echo 'mysqli Connection FAILED. Check parameters inside connect_params.json file.' . PHP_EOL;
    die();
}

function _full_name( $row ) {
    $label = '';

    if ( 1 === $row['grid_id'] ) {
        return 'World';
    }

    if ( ! empty( $row['admin0_name'] ) ) {
        $label = $row['admin0_name'];
    }
    if ( ! empty( $row['admin1_name'] ) ) {
        $label = $row['admin1_name']  . ', ' . $row['admin0_name'];
    }
    if ( ! empty( $row['admin2_name'] ) ) {
        $label = $row['admin2_name'] . ', ' . $row['admin1_name']  . ', ' . $row['admin0_name'];
    }
    if ( ! empty( $row['admin3_name'] ) ) {
        $label = $row['admin3_name'] . ', '. $row['admin2_name'] . ', ' . $row['admin1_name']  . ', ' . $row['admin0_name'];
    }
    if ( ! empty( $row['admin4_name'] ) ) {
        $label = $row['admin4_name'] . ', ' .$row['admin3_name'] . ', ' .$row['admin2_name'] . ', ' . $row['admin1_name']  . ', ' . $row['admin0_name'];
    }
    if ( ! empty( $row['admin5_name'] ) ) {
        $label = $row['admin5_name'] . ', ' .$row['admin4_name'] . ', ' .$row['admin3_name'] . ', ' .$row['admin2_name'] . ', ' . $row['admin1_name']  . ', ' . $row['admin0_name'];
    }

    return $label;
}

function _format_location_grid_types( $row ) {
    if ( ! empty( $row ) || ! is_array( $row ) ) {
        if ( isset( $row['grid_id'] ) ) {
            $row['grid_id'] = (int) $row['grid_id'];
        }
        if ( isset( $row['level'] ) ) {
            $row['level'] = (int) $row['level'];
        }
        if ( isset( $row['population'] ) ) {
            $row['population'] = (int) $row['population'];
            $row['population_formatted'] = number_format( (int) $row['population'] );
        }
        if ( isset( $row['latitude'] ) ) {
            $row['latitude'] = (float) $row['latitude'];
        }
        if ( isset( $row['longitude'] ) ) {
            $row['longitude'] = (float) $row['longitude'];
        }
        if ( isset( $row['north_latitude'] ) ) {
            $row['north_latitude'] = (float) $row['north_latitude'];
        }
        if ( isset( $row['east_longitude'] ) ) {
            $row['east_longitude'] = (float) $row['east_longitude'];
        }
        if ( isset( $row['south_latitude'] ) ) {
            $row['south_latitude'] = (float) $row['south_latitude'];
        }
        if ( isset( $row['west_longitude'] ) ) {
            $row['west_longitude'] = (float) $row['west_longitude'];
        }
        if ( isset( $row['parent_id'] ) ) {
            $row['parent_id'] = (int) $row['parent_id'];
        }
        if ( isset( $row['geoJSON'] ) ) {
            $row['geoJSON'] = (bool) ( 'true' === $row['geoJSON'] ) ? true : false ;
        }
        if ( isset( $row['admin0_grid_id'] ) ) {
            $row['admin0_grid_id'] = empty( $row['admin0_grid_id'] ) ? null : (int) $row['admin0_grid_id'];
        }
        if ( isset( $row['admin1_grid_id'] ) ) {
            $row['admin1_grid_id'] = empty( $row['admin1_grid_id'] ) ? null : (int) $row['admin1_grid_id'];
        }
        if ( isset( $row['admin2_grid_id'] ) ) {
            $row['admin2_grid_id'] = empty( $row['admin2_grid_id'] ) ? null : (int) $row['admin2_grid_id'];
        }
        if ( isset( $row['admin3_grid_id'] ) ) {
            $row['admin3_grid_id'] = empty( $row['admin3_grid_id'] ) ? null : (int) $row['admin3_grid_id'];
        }
        if ( isset( $row['admin4_grid_id'] ) ) {
            $row['admin4_grid_id'] = empty( $row['admin4_grid_id'] ) ? null : (int) $row['admin4_grid_id'];
        }
        if ( isset( $row['admin5_grid_id'] ) ) {
            $row['admin5_grid_id'] = empty( $row['admin5_grid_id'] ) ? null : (int) $row['admin5_grid_id'];
        }
    }
    return $row;
}

