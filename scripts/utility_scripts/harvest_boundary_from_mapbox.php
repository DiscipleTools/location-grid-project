<?php
require_once( 'con.php' );
$mapbox_key = 'pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA';

print date('H:i:s') . PHP_EOL;

$results_object = mysqli_query( $con, "
        SELECT  g.geonameid, g.name, g.longitude, g.latitude, g.country_code
        FROM sg_missing_polygons as g
        WHERE reverse_mapbox_response IS NULL
    " );

$results = mysqli_fetch_all($results_object, MYSQLI_ASSOC);
foreach ( $results as $row ) {


    $json = shell_exec('curl https://api.mapbox.com/geocoding/v5/mapbox.places/'. $row['longitude']. ',' . $row['latitude'] . '.json?access_token=' . $mapbox_key  );

    $added = mysqli_query( $con, "UPDATE sg_missing_polygons SET reverse_mapbox_response='{$json}' WHERE geonameid={$row['geonameid']}");
    print_r($added);
}

// https://api.mapbox.com/geocoding/v5/mapbox.places/Colorado.json?access_token=pk.eyJ1IjoiY2hyaXNjaGFzbSIsImEiOiJjanNsczFtdDQwM3djNDRuMG56eXJvcDRmIn0.vdBOPuShPP3WS4oEbCcDjA
// $ mapbox geocode-api '9134 Woodland Dr. Highlands Ranch, CO'

// https://wambachers-osm.website/boundaries/exportBoundaries?cliVersion=1.0&cliKey=94685a49-28cf-463e-ad3a-e7efe7c598ff&exportFormat=json&exportLayout=levels&exportAreas=land&union=false&selected=3061846,4103407,3061757,3726184,3824513,3726170,4103336,4103403,4103404,3726124,3062185,3060793,3726186,3061758,3726189,4103337,4103406,3060792,3061827,3824207,3726175,3061826,3726211,4103405,3062184,3824206,3584607
// curl -f -o file.zip --url 'URL'
// curl -f -o file.zip --url 'https://wambachers-osm.website/boundaries/exportBoundaries?cliVersion=1.0&cliKey=94685a49-28cf-463e-ad3a-e7efe7c598ff&exportFormat=json&exportLayout=levels&exportAreas=land&union=false&selected=3061846,4103407,3061757,3726184,3824513,3726170,4103336,4103403,4103404,3726124,3062185,3060793,3726186,3061758,3726189,4103337,4103406,3060792,3061827,3824207,3726175,3061826,3726211,4103405,3062184,3824206,3584607'