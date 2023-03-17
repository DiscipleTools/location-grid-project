<?php

require '../vendor/autoload.php';

$geotools = new \League\Geotools\Geotools();
$coordA = new \League\Geotools\Coordinate\Coordinate([48.8234055, 2.3072664]);
$coordB = new \League\Geotools\Coordinate\Coordinate([43.296482, 5.36978]);
$distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);

printf("%s\n", $distance->flat()); // 659166.50038742 (meters)
printf("%s\n", $distance->greatCircle()); // 659021.90812846
printf("%s\n", $distance->in('km')->haversine()); // 659.02190812846
printf("%s\n", $distance->in('mi')->vincenty()); // 409.05330679648
printf("%s\n", $distance->in('ft')->flat()); // 2162619.7519272