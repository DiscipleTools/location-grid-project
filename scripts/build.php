<?php

require_once( 'con.php' );
if ( ! isset( $_GET['type'] ) ||  ! isset( $_GET['type'] ) ) {
    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
}