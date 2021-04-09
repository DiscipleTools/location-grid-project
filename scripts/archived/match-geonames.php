<?php

require_once( 'con.php' );

require_once( 'location-grid-geocoder.php' );
$geocoder = new Location_Grid_Geocoder();

print date('H:i:s') . ' | Begin' . PHP_EOL;

$admin_level = $argv[1];

$results_object = mysqli_query( $con, "
        SELECT geonameid, longitude, latitude 
        FROM saturation_grid_geonames WHERE 
        level = '{$admin_level}'
        AND geonameid NOT IN (SELECT geonames_ref FROM location_grid WHERE level = '{$admin_level}' AND geonames_ref IS NOT NULL);
    " );
$results = mysqli_fetch_all($results_object, MYSQLI_ASSOC);

foreach ( $results as $row ) {
	$latitude = (float) $row['latitude'];
	$longitude = (float) $row['longitude'];
	
	$query = mysqli_query( $con, "
        SELECT g.grid_id, g.name FROM location_grid as g
		WHERE 
        g.north_latitude >= {$latitude} AND
        g.south_latitude <= {$latitude} AND
        g.west_longitude >= {$longitude} AND
        g.east_longitude <= {$longitude} AND
        level = '{$admin_level}';
        ");

	if ( $query->num_rows !== 1 ) {
		// test 2
		$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
		$test_2 = $geocoder->lnglat_test2($results, $longitude, $latitude );

		if ( ! empty( $test_2 ) ) {
			update ( $con, $row['geonameid'], $test_2['grid_id'] );
			continue;
		}

		// test 3
		$grid_id = $geocoder->grid_id_from_nearest_polygon_line( $results, $longitude, $latitude );
		if ( $grid_id ) {
			update ( $con, $row['geonameid'], $grid_id );
			continue;
		}


		print date('H:i:s') . ' | ('.$row['geonameid'] . ') FAIL ' . $query->num_rows . ' rows.'  . PHP_EOL;
		continue;
	}

	$record = mysqli_fetch_assoc($query );

	update ( $con, $row['geonameid'], $record['grid_id'] );

}

function update ( $con, $geonameid, $grid_id ) {
	$sql = "UPDATE location_grid SET geonames_ref={$geonameid} WHERE grid_id={$grid_id} ";
	$update = mysqli_query( $con, $sql);
	if ( $update ) {
		print date('H:i:s') . ' | Success ' . $grid_id . PHP_EOL;
	} else {
		print date('H:i:s') . ' | FAIL ' . $geonameid . PHP_EOL;
	}
}



print date('H:i:s') . ' | End' . PHP_EOL;