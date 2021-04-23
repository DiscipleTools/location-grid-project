<?php
// @link https://github.com/TobiaszCudnik/phpquery/blob/master/demo.php
require( '../vendor/phpquery/phpQuery/phpQuery.php');


$url = 'https://www.google.com/search?q=douglas%2C+colorado%2C+united+states+population';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

$doc = phpQuery::newDocumentHTML($output);
print_r($doc['#rso']);

//print  pq('#rso');