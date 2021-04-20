<?php
/**
 * Command Line
 *
 * $ php geojson_build.php {table}
 */

require_once 'con.php';

$table = $argv[1];

/**
 * Prepare Countries
 *
$query = mysqli_query( $con, "
    SELECT * FROM {$table} WHERE level = 'admin0'
" );
if ( empty( $query ) ) {
    print_r( $con );
    die();
}
$countries = mysqli_fetch_all( $query, MYSQLI_ASSOC );
foreach ( $countries as $country ) {

    $update_query = mysqli_query( $con, "
                UPDATE {$table} SET parent_id = 1,
                admin0_grid_id = {$country['grid_id']}
                WHERE grid_id = {$country['grid_id']};
            " );

    if ( empty( $update_query ) ) {
        print_r( $con );
        die();
    }
}



/**
 * Prepare Admin1
 *

$query = mysqli_query( $con, "
    SELECT * FROM {$table} WHERE level = 'admin0'
" );
if ( empty( $query ) ) {
    print_r( $con );
    die();
}
$countries = mysqli_fetch_all( $query, MYSQLI_ASSOC );
foreach ( $countries as $country ) {
    print $country['name'] .' | Start'. PHP_EOL;

        $query = mysqli_query( $con, "
            SELECT * FROM {$table} WHERE level = 'admin1' AND admin0_code = '{$country['admin0_code']}'
        " );
        if ( empty( $query ) ) {
            print_r( $con );
            die();
        }
        $admin1s = mysqli_fetch_all( $query, MYSQLI_ASSOC );
        foreach( $admin1s as $admin1 ) {
            $code = $country['country_code'] ?? NULL;

            $update_query = mysqli_query( $con, "
                    UPDATE {$table} SET parent_id = {$country['grid_id']},
                    admin0_grid_id = {$country['grid_id']},
                    admin1_grid_id = {$admin1['grid_id']},
                    country_code = '{$code}'
                    WHERE grid_id = {$admin1['grid_id']};
                " );

            if ( empty( $update_query ) ) {
                print_r( $con );
                die();
            }

        }
    print $country['name'] .' | End'. PHP_EOL;
}


/**
 * Prepare Admin2 alt
 *
$query = mysqli_query( $con, "
    SELECT * FROM {$table} WHERE level = 'admin0'
" );
if ( empty( $query ) ) {
    print_r( $con );
    die();
}
$countries = mysqli_fetch_all( $query, MYSQLI_ASSOC );
foreach ( $countries as $country ) {
    print $country['name'] .' | Start'. PHP_EOL;

    $admin2_query = mysqli_query( $con, "
                SELECT * FROM {$table} WHERE level = 'admin2' AND admin0_code = '{$country['admin0_code']}'
            " );
    if ( empty( $admin2_query ) ) {
        print 'No admin3 for ' . $country['name'] . PHP_EOL;
        continue;
    }
    $admin2s = mysqli_fetch_all( $admin2_query, MYSQLI_ASSOC );

    foreach ( $admin2s as $admin2 ) {

        $admin1_query = mysqli_query( $con, "
                SELECT * FROM {$table} WHERE admin0_code = '{$admin2['admin0_code']}' AND admin1_code = '{$admin2['admin1_code']}' LIMIT 1
            " );
        if ( empty( $admin1_query ) ) {
            print 'No admin3 for ' . $country['name'] . PHP_EOL;
            continue;
        }
        $admin1 = mysqli_fetch_assoc( $admin1_query );

        $update_query = mysqli_query( $con, "
                UPDATE {$table} SET parent_id = '{$admin1['grid_id']}',
                admin0_grid_id = '{$admin1['admin0_grid_id']}',
                admin1_grid_id = {$admin1['admin1_grid_id']},
                admin2_grid_id = {$admin2['grid_id']}
                WHERE grid_id = {$admin2['grid_id']};
            " );

        if ( empty( $update_query ) ) {
            print_r( $con );
            die();
        }
        print $admin2['name'] . PHP_EOL;
    }

    print $country['name'] .' | End'. PHP_EOL;
}

die();

/**
 * Prepare Admin3
 *
$query = mysqli_query( $con, "
    SELECT * FROM {$table} WHERE level = 'country'
" );
if ( empty( $query ) ) {
    print_r( $con );
    die();
}
$countries = mysqli_fetch_all( $query, MYSQLI_ASSOC );
foreach ( $countries as $country ) {
    print $country['name'] .' | Start'. PHP_EOL;

    $admin3_query = mysqli_query( $con, "
                SELECT * FROM {$table} WHERE level = 'admin3' AND admin0_code = '{$country['admin0_code']}'
            " );
    if ( empty( $admin3_query ) ) {
        print 'No admin3 for ' . $country['name'] . PHP_EOL;
        continue;
    }
    $admin3s = mysqli_fetch_all( $admin3_query, MYSQLI_ASSOC );



    foreach ( $admin3s as $admin3 ) {

        $admin2_query = mysqli_query( $con, "
                SELECT * FROM {$table} WHERE admin0_code = '{$admin3['admin0_code']}' AND admin1_code = '{$admin3['admin1_code']}' AND admin2_code = '{$admin3['admin2_code']}' LIMIT 1
            " );
        if ( empty( $admin2_query ) ) {
            print 'No admin3 for ' . $country['name'] . PHP_EOL;
            continue;
        }
        $admin2 = mysqli_fetch_assoc( $admin2_query );

        $update_query = mysqli_query( $con, "
                UPDATE {$table} SET parent_id = '{$admin2['grid_id']}',
                admin0_grid_id = '{$admin2['admin0_grid_id']}',
                admin1_grid_id = {$admin2['admin1_grid_id']},
                admin2_grid_id = {$admin2['admin2_grid_id']},
                admin3_grid_id = {$admin3['grid_id']}
                WHERE grid_id = {$admin3['grid_id']};
            " );

        if ( empty( $update_query ) ) {
            print_r( $con );
            die();
        }
        print $admin3['name'] . PHP_EOL;
    }

    print $country['name'] .' | End'. PHP_EOL;
}
die();

/**
 * Admin 4
 *
$query = mysqli_query( $con, "
    SELECT * FROM {$table} WHERE level = 'country'
" );
if ( empty( $query ) ) {
    print_r( $con );
    die();
}
$countries = mysqli_fetch_all( $query, MYSQLI_ASSOC );
foreach ( $countries as $country ) {
    print $country['name'] .' | Start'. PHP_EOL;

    $admin4_query = mysqli_query( $con, "
                SELECT * FROM {$table} WHERE level = 'admin4' AND admin0_code = '{$country['admin0_code']}'
            " );
    if ( empty( $admin4_query ) ) {
        print 'No admin4 for ' . $country['name'] . PHP_EOL;
        continue;
    }
    $admin4s = mysqli_fetch_all( $admin4_query, MYSQLI_ASSOC );



    foreach ( $admin4s as $admin4 ) {

        $admin3_query = mysqli_query( $con, "
                SELECT * FROM {$table} WHERE admin0_code = '{$admin4['admin0_code']}' AND admin1_code = '{$admin4['admin1_code']}' AND admin2_code = '{$admin4['admin2_code']}' AND admin3_code = '{$admin4['admin3_code']}' AND admin4_code = '' LIMIT 1
            " );
        if ( empty( $admin3_query ) ) {
            print 'No admin3 for ' . $country['name'] . PHP_EOL;
            continue;
        }
        $admin3 = mysqli_fetch_assoc( $admin3_query );

        $update_query = mysqli_query( $con, "
                UPDATE {$table} SET parent_id = '{$admin3['grid_id']}',
                admin0_grid_id = '{$admin3['admin0_grid_id']}',
                admin1_grid_id = {$admin3['admin1_grid_id']},
                admin2_grid_id = {$admin3['admin2_grid_id']},
                admin3_grid_id = {$admin3['admin3_grid_id']},
                admin4_grid_id = {$admin4['grid_id']}
                WHERE grid_id = {$admin4['grid_id']};
            " );

        if ( empty( $update_query ) ) {
            print_r( $con );
            die();
        }
        print $admin4['name'] . PHP_EOL;
    }

    print $country['name'] .' | End'. PHP_EOL;
}


/**
 * Admin 5
 *
$query = mysqli_query( $con, "
    SELECT * FROM {$table} WHERE level = 'country'
" );
if ( empty( $query ) ) {
    print_r( $con );
    die();
}
$countries = mysqli_fetch_all( $query, MYSQLI_ASSOC );
foreach ( $countries as $country ) {
    print $country['name'] .' | Start'. PHP_EOL;

    $admin5_query = mysqli_query( $con, "
                SELECT * FROM {$table} WHERE level = 'admin5' AND admin0_code = '{$country['admin0_code']}'
            " );
    if ( empty( $admin5_query ) ) {
        print 'No admin5 for ' . $country['name'] . PHP_EOL;
        continue;
    }
    $admin5s = mysqli_fetch_all( $admin5_query, MYSQLI_ASSOC );

    foreach ( $admin5s as $admin5 ) {

        $admin4_query = mysqli_query( $con, "
                SELECT * FROM {$table} 
                WHERE admin0_code = '{$admin5['admin0_code']}' 
                AND admin1_code = '{$admin5['admin1_code']}' 
                AND admin2_code = '{$admin5['admin2_code']}' 
                AND admin3_code = '{$admin5['admin3_code']}' 
                AND admin4_code = '{$admin5['admin4_code']}' 
                LIMIT 1
            " );
        if ( empty( $admin4_query ) ) {
            print 'No admin4 for ' . $country['name'] . PHP_EOL;
            continue;
        }
        $admin4 = mysqli_fetch_assoc( $admin4_query );

        $update_query = mysqli_query( $con, "
                UPDATE {$table} SET parent_id = '{$admin4['grid_id']}',
                admin0_grid_id = '{$admin4['admin0_grid_id']}',
                admin1_grid_id = {$admin4['admin1_grid_id']},
                admin2_grid_id = {$admin4['admin2_grid_id']},
                admin3_grid_id = {$admin4['admin3_grid_id']},
                admin4_grid_id = {$admin4['admin4_grid_id']},
                admin5_grid_id = {$admin5['grid_id']}
                WHERE grid_id = {$admin5['grid_id']};
            " );

        if ( empty( $update_query ) ) {
            print_r( $con );
            die();
        }

        print $admin5['name'] . PHP_EOL;
    }

    print $country['name'] .' | End'. PHP_EOL;
}

die();







/**
 * Build bbox and lng/lat
 */
include_once( getcwd() . '/vendor/phayes/geophp/geoPHP.inc'); // make sure to run $ composer install on the command line

$admin2_query = mysqli_query( $con, "
        SELECT * FROM {$table} 
    " );
if ( empty( $admin2_query ) ) {
    print_r( $con );
    die();
}
$admin2 = mysqli_fetch_all( $admin2_query, MYSQLI_ASSOC );
foreach( $admin2 as $result ) {
    $grid_id = $result['grid_id'];

    if ( empty($result['geoJSON']) ) {
        print date('H:i:s') . ' | No geoJSON file. ' . $result['name'] . PHP_EOL;
        continue;
    }

    try {
        $polygon = geoPHP::load($result['geoJSON'], 'json');
    } catch ( Exception $e ) {
        print date('H:i:s') . ' | Fail ' . '(' . $grid_id . ' - ' . $result['name'] . ')' . PHP_EOL;
        continue;
    }

    $box = $polygon->getBBox();
    $nla = $box['maxy'];
    $sla = $box['miny'];
    $elo = $box['maxx'];
    $wlo = $box['minx'];

    $centroid = $polygon->centroid();
    $lng = $centroid->coords[0];
    $lat = $centroid->coords[1];

    /* Update db */
    $update = mysqli_query( $con, "UPDATE {$table} SET
        north_latitude={$nla},
        south_latitude={$sla},
        east_longitude={$elo},
        west_longitude={$wlo},
        latitude={$lat},
        longitude={$lng}
        WHERE grid_id = {$grid_id};");


    if ( empty( $update ) ) {
        print_r($con);
        die();
    }
}

print 'Success bbox and lng/lat' . PHP_EOL;

/**
 *
 */

mysqli_close($con );

