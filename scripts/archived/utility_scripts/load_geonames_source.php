<?php

$cons= mysqli_connect("localhost:3306", "root","chasm","city_database") or die();

$result1=mysqli_query($cons,"
 LOAD DATA LOCAL INFILE 'allCountries.txt'
            INTO TABLE geonames          
            FIELDS TERMINATED BY '\t'
            LINES TERMINATED BY '\n'
            (geonameid,name,asciiname,alternatenames,latitude,longitude,feature_class,feature_code,country_code,cc2,admin1_code,admin2_code,admin3_code,admin4_code,population,elevation,dem,timezone,modification_date)

");
$result = mysqli_query($cons,"Select count(*) FROM `geonames`");

print_r($result);