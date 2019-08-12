<?php
// return bbox polygon
if ( isset( $_GET['type'] ) && isset( $_GET['longitude'] ) && isset( $_GET['latitude'] ) && $_GET['type'] === 'bbox' ) {

    $level = null;
    if ( isset( $_GET['level'] ) ) {
        $level = $_GET['level'];
    }
    $longitude = $_GET['longitude'];
    $latitude =  $_GET['latitude'];

    require_once( 'location-grid-geocoder.php' );
    $geocoder = new Location_Grid_Geocoder();
    $record =  $geocoder->get_geonameid_by_lnglat( $longitude, $latitude, $level );

    $query = mysqli_query( $con, "
            SELECT g.*, c.name as country_name, a1.name as admin1_name, a2.name as admin2_name
            FROM {$tables['geonames']} as g
            LEFT JOIN {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
            LEFT JOIN {$tables['geonames']} as a1 ON g.admin1_geonameid=a1.geonameid
            LEFT JOIN {$tables['geonames']} as a2 ON g.admin2_geonameid=a2.geonameid
            WHERE g.geonameid = {$record['geonameid']};
        " );
    if ( $query ) {
        header('Content-type: application/json');
        $item = mysqli_fetch_assoc( $query );

        $html = '{"type":"FeatureCollection","features":[';
        $html .= '{"type": "Feature","geometry": ';
        $html .= '{"type":"Polygon","coordinates":[[['.$item['west_longitude'].','.$item['north_latitude'].'],['.$item['east_longitude'].','.$item['north_latitude'].'],['.$item['east_longitude'].','.$item['south_latitude'].'],['.$item['west_longitude'].','.$item['south_latitude'].'],['.$item['west_longitude'].','.$item['north_latitude'].']]]}';
        $html .= ',"properties":{';
        $html .= '"name":"' . $item[ 'name' ] . '",';
        $html .= '"id":"' . $item[ 'geonameid' ] . '",';
        $html .= '"country_code":"' . $item[ 'country_code' ] . '",';
        $html .= '"admin1_code":"' . $item[ 'admin1_code' ] . '",';
        $html .= '"admin2_code":"' . $item[ 'admin2_code' ] . '",';
        $html .= '"center_lat":' . (float) $item[ 'latitude' ] . ',';
        $html .= '"center_lng":' . (float) $item[ 'longitude' ] . ',';
        $html .= '"geonameid":' . $item[ 'geonameid' ];
        $html .= '}';
        $html .= ',"id":"' . $item[ 'geonameid' ] . '"';

        $html .= '}';
        $html .= ']}';

        print $html;
        return;
    }
}