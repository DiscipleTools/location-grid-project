<?php
/**
 * Command Line
 *
 * $ php geojson_build.php {table}
 */

require_once 'con.php';

$table = $argv[1];

// add country_code column
$code = mysqli_query( $con, "
        SELECT * FROM country_codes
    " );
if ( empty( $code ) ) {
    print_r( $con );
    die();
}
$codes = mysqli_fetch_all( $code, MYSQLI_ASSOC );

foreach ( $codes as $value ) {
    print $value['country_code2'] . ' ' . PHP_EOL;
    $cc = mysqli_query( $con, "
        UPDATE {$table} SET country_code = '{$value['country_code2']}'
        WHERE country_code3 = '{$value['country_code3']}';
    " );
}
