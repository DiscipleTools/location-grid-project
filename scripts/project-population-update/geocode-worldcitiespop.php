<?php
include('con.php');
//include('../location-grid-geocoder-non-wp.php');
//
//$geocoder = new Location_Grid_Geocoder();

$query_raw = mysqli_query( $con,
    "SELECT lg.*
                FROM location_grid as lg 
                WHERE lg.population < 1
                AND level = 0
                " );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );


foreach ( $query as $row ){

    $cities_raw = mysqli_query( $con,
        "SELECT population FROM worldcitiespop_only_pop
                WHERE latitude < {$row['north_latitude']} AND latitude > {$row['south_latitude']}
                AND longitude < {$row['east_longitude']} AND longitude > {$row['west_longitude']}
                " );
    if ( empty( $cities_raw ) ) {
        print_r( $con );
        die();
    }
    $cities = mysqli_fetch_all( $cities_raw, MYSQLI_ASSOC );

    $population = 0;

    if ( ! empty( $cities ) ){
        foreach( $cities as $city ){
            if ( ! empty( $city['population'] ) && is_numeric( $city['population'] ) ) {
                $population = $population + $city['population'];
            }
        }
    }

    if ( ! empty( $population ) ){
        mysqli_query( $con, "UPDATE location_grid SET `population` = '{$population}' WHERE `grid_id` = {$row['grid_id']};");
        print $row['grid_id'] . ' | ' . $row['name'] . ' | Current Pop: ' . $row['population'] . ' | New Pop: ' . $population . PHP_EOL;
    }
    else {
        print '.';
    }

}

print 'End'. PHP_EOL;