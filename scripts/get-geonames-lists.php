<?php

require_once( 'con.php' );
if ( ! isset( $_GET['type'] ) ||  ! isset( $_GET['type'] ) ) {
    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
}

$type = $_GET['type'];
$value =  $_GET['value'];

if ( $type === 'geonameid' ) {
    $query = mysqli_query( $con, "
        SELECT *
        FROM {$tables['geonames']} as g
        WHERE g.parent_id = {$value} OR g.geonameid = {$value};
    " );
    if ( $query === false ) {
        die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
    }
    $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

    header('Content-type: application/json');

    echo json_encode($result);
}

if ( $type === 'countries' ) {

    $query = mysqli_query( $con, "
        SELECT c.*
        FROM {$tables['geonames']} as c
        WHERE c.level = 'admin0'
        ORDER BY c.name;
    " );
    if ( $query === false ) {
        die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
    }
    $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

    header('Content-type: application/json');

    echo json_encode($result);
}

if ( $type === 'states' ) {
    $query = mysqli_query( $con, "
        SELECT *
        FROM {$tables['geonames']} as g
        WHERE g.admin0_code = '{$value}' AND g.level = 'admin1';
    " );
    if ( $query === false ) {
        die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
    }
    $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

    header('Content-type: application/json');

    echo json_encode($result);
}

if ( $type === 'counties' ) {
    $query = mysqli_query( $con, "
        SELECT *
        FROM {$tables['geonames']} as g
        WHERE g.admin0_code = '{$value}' AND g.level = 'admin2';
    " );
    if ( $query === false ) {
        die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
    }
    $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

    header('Content-type: application/json');

    echo json_encode($result);
}

if ( $type === 'continents' ) {
    $query = mysqli_query( $con, "
        SELECT c.*
        FROM {$tables['geonames']} as c
        WHERE c.parent_id IN (
            SELECT g.geonameid
            FROM {$tables['geonames']} as g
            WHERE g.parent_id = {$value}
        );
    " );
    if ( $query === false ) {
        die(json_encode(array('message' => 'NO CONTENT', 'code' => 204) ) );
    }
    $result = mysqli_fetch_all( $query, MYSQLI_ASSOC );

    header('Content-type: application/json');

    echo json_encode($result);
}

