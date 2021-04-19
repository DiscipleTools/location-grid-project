<?php
print 'Start' . PHP_EOL;

require_once( 'con.php' );

$table = 'morocco';
include_once( '../vendor/phayes/geophp/geoPHP.inc' ); // make sure to run $ composer install on the command line


$query  = mysqli_query( $con, "
            SELECT * FROM {$table}" );
$results = mysqli_fetch_all( $query, MYSQLI_ASSOC );

foreach( $results as $result ) {
    $features = [];

//    $grid_id = $result['grid_id'];
    $geometry = $result['geoJSON'];

    $props = $result;
    unset($props['geoJSON']);

    $features[] = array(
        "type" => "Feature",
        "properties" => $props,
        "geometry" => json_decode( $geometry, true ),
    );

    $geojson = array(
        'type' => "FeatureCollection",
        'features' => $features,
    );

    $geojson = json_encode( $geojson );

    // $centroid['coords'][0] = lng
    // $centroid['coords'][1] = lat
    // $bounds['maxy'] = n
    // $bounds['minx'] = s
    // $bounds['maxx'] = e
    // $bounds['minx'] = w

    $geometry = geoPHP::load( $geojson, 'geojson' );
    $centroid = $geometry->getCentroid();
//    print_r($centroid);

    $bounds = $geometry->getBBox();
//    print_r($bounds);


    mysqli_query( $con, "
        UPDATE {$table} 
        SET 
            longitude = {$centroid->coords[0]},
            latitude = {$centroid->coords[1]},
            north_latitude = {$bounds['maxy']},
            south_latitude = {$bounds['miny']},
            east_longitude = {$bounds['maxx']},
            west_longitude = {$bounds['minx']}
        WHERE grid_id = {$result['grid_id']}
        " );

    print '#';
}

mysqli_close($con);
print PHP_EOL . 'End' . PHP_EOL;
