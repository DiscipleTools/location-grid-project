<?php
/**
 * Create json_db and json_db/children folders in the location-grid-mirror
 *
 * create a folder with json files for each row of the location grid db, named by the location grid id.
 */
require_once( 'con.php' );

print 'BEGIN' . PHP_EOL;

/** FOLDERS */
$output = [
    'output' => getcwd() . '/output/json_db/',
];

// override destination output
if ( isset( $argv[1] ) ) {
    $output = [
        'output' => $argv[1],
    ];
}

foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

$query_raw = mysqli_query( $con,
    "SELECT 
                lg.*, 
                if ( g.geoJSON IS NOT NULL, 'true', 'false' ) as geoJSON, 
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
                " );

if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

foreach ( $query as $row ){

    $row = _format_location_grid_types($row);

    $row['full_name'] = _full_name( $row );

    unset( $row['admin1_code'] );
    unset( $row['admin2_code'] );
    unset( $row['admin3_code'] );
    unset( $row['admin4_code'] );
    unset( $row['admin5_code'] );
    unset( $row['modification_date'] );

    $json = json_encode( $row );

    file_put_contents( $output['output'] . $row['grid_id'] . '.json', $json );

    print '#';
}

print 'END' . PHP_EOL;


function _full_name( $row ) {
    $label = '';

    if ( 1 === $row['grid_id'] ) {
        $label = 'World';
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
print PHP_EOL . 'END BASE' . PHP_EOL;



print 'BEGIN CHILDREN' . PHP_EOL;

/** FOLDERS */
$output = [
    'output' => getcwd() . '/output/json_db/children/',
];

// override destination output
if ( isset( $argv[1] ) ) {
    $output = [
        'output' => $argv[1] . 'children/',
    ];
}

foreach ( $output as $dirname ) {
    if ( ! is_dir( $dirname ) ) {
        mkdir($dirname, 0755, true);
    }
}

$query_raw = mysqli_query( $con,
    "SELECT 
                lg.*, 
                if ( g.geoJSON IS NOT NULL, 'true', 'false' ) as geoJSON, 
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
                " );

if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

foreach ( $query as $row ){

    $row = _format_location_grid_types($row);

    $row['full_name'] = _full_name( $row );
    $row['children_total'] = 0;
    $row['children'] = [];

    unset( $row['admin1_code'] );
    unset( $row['admin2_code'] );
    unset( $row['admin3_code'] );
    unset( $row['admin4_code'] );
    unset( $row['admin5_code'] );
    unset( $row['modification_date'] );

    $children_raw = mysqli_query( $con,
        "SELECT 
                lg.*, 
                if ( g.geoJSON IS NOT NULL, 'true', 'false' ) as geoJSON, 
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
                WHERE lg.parent_id = {$row['grid_id']}
                " );

    if ( ! empty( $children_raw ) ) {
        $children = mysqli_fetch_all( $children_raw, MYSQLI_ASSOC );
        $row['children_total'] = count($children);
        foreach ( $children as $child ) {

            $child = _format_location_grid_types($child);

            $child['full_name'] = _full_name($child);

            unset($child['admin1_code']);
            unset($child['admin2_code']);
            unset($child['admin3_code']);
            unset($child['admin4_code']);
            unset($child['admin5_code']);
            unset($child['modification_date']);

            $row['children'][$child['grid_id']] = $child;

        }
    }


    $json = json_encode( $row );

    file_put_contents( $output['output'] . $row['grid_id'] . '.json', $json );

    print '#';

}

print PHP_EOL . 'END CHILDREN' . PHP_EOL;