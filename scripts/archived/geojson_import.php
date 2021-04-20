<?php
/**
 * Command Line
 *
 * $ php import-geojson.php {start_increment_number} {table_name} {country_code} {time_zone}
 */

require_once 'con.php';

$content = [];
$dir = scandir($output['geojson_upload'] );
if ( empty( $dir ) ) {
    die('No files');
}
foreach ( $dir as $i => $v ) {
    if ( substr( $v, -4, 4 ) !== 'json' ) {
        unset( $dir[$i] );
    }
}



if ( ! isset( $argv[1] ) ) { // start increment
    die('starting increment required' );
}
else {
    $start_increment = $argv[1];
}
if ( ! isset( $argv[2] ) ) { // table name
    $table = 'geojson';
}
else {
    $table = $argv[2];
}


$results = mysqli_query( $con, "
    CREATE TABLE IF NOT EXISTS `{$table}` (
      `grid_id` bigint(20) NOT NULL AUTO_INCREMENT,
      `name` varchar(200) DEFAULT NULL,
      `level` varchar(50) DEFAULT NULL,
      `country_code` char(2) DEFAULT NULL,
      `admin0_code` char(3) DEFAULT NULL,
      `admin1_code` varchar(20) DEFAULT NULL,
      `admin2_code` varchar(80) DEFAULT NULL,
      `admin3_code` varchar(20) DEFAULT NULL,
      `admin4_code` varchar(20) DEFAULT NULL,
      `admin5_code` varchar(20) DEFAULT NULL,
      `parent_id` bigint(20) DEFAULT NULL,
      `admin0_grid_id` bigint(20) DEFAULT NULL,
      `admin1_grid_id` bigint(20) DEFAULT NULL,
      `admin2_grid_id` bigint(20) DEFAULT NULL,
      `admin3_grid_id` bigint(20) DEFAULT NULL,
      `admin4_grid_id` bigint(20) DEFAULT NULL,
      `admin5_grid_id`bigint(20) DEFAULT NULL,
      `latitude` float DEFAULT NULL,
      `longitude` float DEFAULT NULL,
      `north_latitude` float DEFAULT NULL,
      `south_latitude` float DEFAULT NULL,
      `west_longitude` float DEFAULT NULL,
      `east_longitude` float DEFAULT NULL,
      `population` bigint(20) NOT NULL DEFAULT '0',
      `geonames_ref` varchar(20) DEFAULT NULL,
      `wikidata_ref` varchar(20) DEFAULT NULL,
      `modification_date` date DEFAULT NULL,
      `geoJSON` longtext,
      `country_name` varchar(200) DEFAULT NULL,
      `admin1_name` varchar(200) DEFAULT NULL,
      `admin2_name` varchar(200) DEFAULT NULL,
      `admin3_name` varchar(200) DEFAULT NULL,
      `admin4_name` varchar(200) DEFAULT NULL,
      `admin5_name` varchar(200) DEFAULT NULL,
      PRIMARY KEY (`grid_id`)
) ENGINE=InnoDB AUTO_INCREMENT={$start_increment} DEFAULT CHARSET=utf8;
" );

//print_r($con);

foreach ( $dir as $file ) {
    print $file . PHP_EOL;

    $geojson = json_decode( file_get_contents( $output['geojson_upload'] . $file ), true );
    if ( empty( $geojson ) ) {
        die( 'no content' );
    }
    $results = [];
    if ( ! empty( $geojson['features'] ) ) {
        foreach ( $geojson['features'] as $feature ) {

            $name = mysqli_escape_string( $con, $feature['properties']['NAME_5'] ?? $feature['properties']['NAME_4'] ?? $feature['properties']['NAME_3'] ?? $feature['properties']['NAME_2'] ?? $feature['properties']['NAME_1'] ?? $feature['properties']['NAME_0'] ?? NULL );
            if ( empty( $name ) || is_numeric( $name ) ) {
                if ( ! empty( $feature['properties']['NAME_5'] ) ) {
                    $name = mysqli_escape_string( $con, $feature['properties']['NAME_5'] );
                    if ( is_numeric( $name ) ) {
                        $name = mysqli_escape_string( $con,$feature['properties']['NAME_4'] ) . ' ' . $name;
                    }
                }
                else if ( ! empty( $feature['properties']['NAME_4'] ) ) {
                    $name = mysqli_escape_string( $con,$feature['properties']['NAME_4'] );
                    if ( is_numeric( $name ) ) {
                        $name = mysqli_escape_string( $con,$feature['properties']['NAME_3'] ) . ' ' . $name;
                    }
                }
                else if ( ! empty( $feature['properties']['NAME_3'] ) ) {
                    $name = mysqli_escape_string( $con,$feature['properties']['NAME_3'] );
                    if ( is_numeric( $name ) ) {
                        $name = mysqli_escape_string( $con,$feature['properties']['NAME_2'] ) . ' ' . $name;
                    }
                }
                else if ( ! empty( $feature['properties']['NAME_2'] ) ) {
                    $name = mysqli_escape_string( $con,$feature['properties']['NAME_2'] );
                    if ( is_numeric( $name ) ) {
                        $name = mysqli_escape_string( $con,$feature['properties']['NAME_1'] ) . ' ' . $name;
                    }
                }
                else if ( ! empty( $feature['properties']['NAME_1'] ) ) {
                    $name = mysqli_escape_string( $con,$feature['properties']['NAME_1'] );
                    if ( is_numeric( $name ) ) {
                        $name = mysqli_escape_string( $con,$feature['properties']['NAME_0'] ) . ' ' . $name;
                    }
                }
                else if ( ! empty( $feature['properties']['NAME_0'] ) ) {
                    $name = mysqli_escape_string( $con,$feature['properties']['NAME_0'] );
                }
                else {
                    die('No name found');
                }
            }
            $admin0_code =  mysqli_escape_string( $con, $feature['properties']['GID_0'] ?? NULL );
            $admin1_code =  mysqli_escape_string( $con, $feature['properties']['GID_1'] ?? NULL );
            $admin2_code =  mysqli_escape_string( $con, $feature['properties']['GID_2'] ?? NULL );
            $admin3_code =  mysqli_escape_string( $con, $feature['properties']['GID_3'] ?? NULL );
            $admin4_code =  mysqli_escape_string( $con, $feature['properties']['GID_4'] ?? NULL );
            $admin5_code =  mysqli_escape_string( $con, $feature['properties']['GID_5'] ?? NULL );
            $modification_date = date('Y-m-d');
            $geoJSON = json_encode($feature['geometry']);
            $country_name =  mysqli_escape_string( $con, $feature['properties']['NAME_0'] ?? NULL );
            $admin1_name =  mysqli_escape_string( $con, $feature['properties']['NAME_1'] ?? NULL );
            $admin2_name =  mysqli_escape_string( $con, $feature['properties']['NAME_2'] ?? NULL );
            $admin3_name =  mysqli_escape_string( $con, $feature['properties']['NAME_3'] ?? NULL );
            $admin4_name =  mysqli_escape_string( $con, $feature['properties']['NAME_4'] ?? NULL );
            $admin5_name =  mysqli_escape_string( $con, $feature['properties']['NAME_5'] ?? NULL );

            $x = mysqli_query( $con, "
            INSERT INTO {$table}
            (
              `name`,
              `admin0_code`,
              `admin1_code`,
              `admin2_code`,
              `admin3_code`,
              `admin4_code`,
              `admin5_code`,
              `country_name`,
              `admin1_name`,
              `admin2_name`,
              `admin3_name`,
              `admin4_name`,
              `admin5_name`,
              `modification_date`,
              `geoJSON`
            )
            VALUES (
              '{$name}',
              '{$admin0_code}',
              '{$admin1_code}',
              '{$admin2_code}',
              '{$admin3_code}',
              '{$admin4_code}',
              '{$admin5_code}',
              '{$country_name}',
              '{$admin1_name}',
              '{$admin2_name}',
              '{$admin3_name}',
              '{$admin4_name}',
              '{$admin5_name}',
              '{$modification_date}',
              '{$geoJSON}'
            )
        " );

            if ( empty( $x ) ) {
                print_r($con);
                die();
            } else {
                $results[$name] = true;
                print $name . PHP_EOL;
            }
        }
    }

}

//print_r($results);

print 'Finish' . PHP_EOL;

mysqli_close($con );

