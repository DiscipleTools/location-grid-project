<?php

include('con.php');

if ( ! isset( $argv[1] ) ) {
    print 'Error: Set the language code' . PHP_EOL;
    die();
}

$column = 'full_name';
$country = '';
$translated_country = '';

$query_raw = mysqli_query( $con,
    "
        SELECT * FROM location_grid_names_working WHERE language_code = '{$argv[1]}'
            " );
if ( empty( $query_raw ) ) {
    print_r( $con );
    die();
}
$query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

foreach( $query as $index => $row ) {

    // translate country
    if ( $country === $row['admin0_name'] ) { // already translated
        mysqli_query( $con,
            "UPDATE location_grid_names SET admin0_name = '{$translated_country}' WHERE id = {$row['id']}" );
        mysqli_query( $con,
            "UPDATE location_grid_names_working SET admin0_name = '{$translated_country}' WHERE id = {$row['id']}" );
    } else {
        $country = $row['admin0_name'];
        $encoded_country =  urlencode($country);
        $result = shell_exec('curl "https://www.googleapis.com/language/translate/v2?key=AIzaSyBArrJ9LTX_oVWk2eJCXGc6O_n8yQhpl1E&source=en&target='.$argv[1].'&q='.$encoded_country.'"');
        $result = json_decode( $result, true );
        if( isset( $result['error'] ) ) {
            print 'Error' . PHP_EOL;
            print_r($result);
            die();
        }
        $translated_country = $result['data']['translations'][0]['translatedText'];

        mysqli_query( $con,
            "UPDATE location_grid_names SET admin0_name = '{$translated_country}' WHERE id = {$row['id']}" );
        mysqli_query( $con,
            "UPDATE location_grid_names_working SET admin0_name = '{$translated_country}' WHERE id = {$row['id']}" );
    }


    // translate full name
    $full_name = urlencode( $row['full_name'] );
    $result_full_name = shell_exec('curl "https://www.googleapis.com/language/translate/v2?key=AIzaSyBArrJ9LTX_oVWk2eJCXGc6O_n8yQhpl1E&source=en&target='.$argv[1].'&q='.$full_name.'"');
    $result_full_name = json_decode( $result_full_name, true );
    $translated_full_name= html_entity_decode($result_full_name['data']['translations'][0]['translatedText']);
    mysqli_query( $con,
        "UPDATE location_grid_names SET full_name = '{$translated_full_name}' WHERE id = {$row['id']}" );
    mysqli_query( $con,
        "UPDATE location_grid_names_working SET full_name = '{$translated_full_name}' WHERE id = {$row['id']}" );

    // translate name
    $name = urlencode( $row['name'] );
    $result_name = shell_exec('curl "https://www.googleapis.com/language/translate/v2?key=AIzaSyBArrJ9LTX_oVWk2eJCXGc6O_n8yQhpl1E&source=en&target='.$argv[1].'&q='.$name.'"');
    $result_name = json_decode( $result_name, true );
    $translated_name = html_entity_decode($result_name['data']['translations'][0]['translatedText']);
    mysqli_query( $con,
        "UPDATE location_grid_names SET name = '{$translated_name}' WHERE id = {$row['id']}" );
    mysqli_query( $con,
        "UPDATE location_grid_names_working SET name = '{$translated_name}' WHERE id = {$row['id']}" );

    print $row['full_name'] . PHP_EOL . PHP_EOL;

//    if ( $index > 10 ) {
//        break;
//    }

}




// curl "https://www.googleapis.com/language/translate/v2?key=&source=en&target=de&q=Hello%20World"
// curl "https://www.googleapis.com/language/translate/v2?key=AIzaSyBArrJ9LTX_oVWk2eJCXGc6O_n8yQhpl1E&source=en&target=fr&q=Hello%20World";
// AIzaSyBArrJ9LTX_oVWk2eJCXGc6O_n8yQhpl1E