<?php

require_once 'con.php';

$admin_level = $argv[1];

$results_object = mysqli_query( $con, "
        UPDATE location_grid
        INNER JOIN saturation_grid_geonames ON saturation_grid_geonames.geonameid=location_grid.geonames_ref
        SET location_grid.population = saturation_grid_geonames.population;
    " );

/**
 *  Select count(*) From saturation_grid_geonames WHERE population > 0;
	Select count(*) From location_grid WHERE population > 0;
 */