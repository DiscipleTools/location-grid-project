<?php

if ( isset( $_GET['type'] ) && isset( $_GET['longitude'] ) && isset( $_GET['latitude'] ) && $_GET['type'] === 'info' )  :

    $level = null;
    if ( isset( $_GET['level'] ) ) {
        $level = $_GET['level'];
    }
    $longitude = $_GET['longitude'];
    $latitude =  $_GET['latitude'];

    require_once( 'location-grid-geocoder.php' );
    $geocoder = new Location_Grid_Geocoder();

    $response_array =  $geocoder->get_grid_id_by_lnglat( $longitude, $latitude, $level );
    if ( $response_array ) {
        content_response( $response_array, $longitude, $latitude );
    }

    return; // return to block the page from serving up the HTML at the bottom.

endif; // html

/**
 * Responds to URL request with requested content
 *
 * @param $response_array
 * @param $longitude
 * @param $latitude
 */
function content_response( $response_array, $longitude, $latitude ) {

    switch ( $response_array['level'] ) {
        case 'admin2':

            // info
            if ( $_GET['type'] === 'info' ){
                echo 'Longitude: ' .$longitude. '<br>Latitude: ' .$latitude. '<br>';
                echo '<br><hr><br>';
                echo  $response_array['country_name'] . ' <br>  |--- ' . $response_array['admin1_name'] . ' <br>  |---  |---  ' . $response_array['name'] . ' (' . $response_array['geonameid'] . ')' . '<br>';
                echo '<br><hr><br><strong>'.$response_array[ 'name' ] .' Info:</strong><br>&nbsp;&nbsp;Population: '.$response_array['population'].'<br>&nbsp;&nbsp;Contacts: 0<br>&nbsp;&nbsp;Groups: 0<br>&nbsp;&nbsp;Churches: 0<br>&nbsp;&nbsp;Trainings: 0<br>&nbsp;&nbsp;Workers: 0<br>';
            }


            break;
        case 'admin1':

            // info
            if ( $_GET['type'] === 'info' ){
                echo 'Longitude: ' .$longitude. '<br>Latitude: ' .$latitude. '<br>';
                echo '<br><hr><br>';
                echo  $response_array['country_name'] . ' <br>  |--- ' . $response_array['name'] . ' (' . $response_array['geonameid'] . ')' . '<br>';
                echo '<br><hr><br><strong>'.$response_array[ 'name' ] .' Info:</strong><br>&nbsp;&nbsp;Population: '.$response_array['population'].'<br>&nbsp;&nbsp;Contacts: 0<br>&nbsp;&nbsp;Groups: 0<br>&nbsp;&nbsp;Churches: 0<br>&nbsp;&nbsp;Trainings: 0<br>&nbsp;&nbsp;Workers: 0<br>';
            }


            break;
    }
}
