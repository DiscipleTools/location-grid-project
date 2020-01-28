<?php
print 'Start' . PHP_EOL;

require_once( 'con.php' );

require_once( 'location-grid-geocoder.php' );
$geocoder = new Location_Grid_Geocoder();


$query  = mysqli_query( $con, "
            SELECT g.*, a0.name as admin0_name, a1.name as admin1_name, a2.name as admin2_name, a3.name as admin3_name, a4.name as admin4_name, a5.name as admin5_name
                FROM {$tables['grid']} as g
                LEFT JOIN {$tables['grid']} as a0 ON g.admin0_grid_id=a0.grid_id
                LEFT JOIN {$tables['grid']} as a1 ON g.admin1_grid_id=a1.grid_id
                LEFT JOIN {$tables['grid']} as a2 ON g.admin2_grid_id=a2.grid_id
                LEFT JOIN {$tables['grid']} as a3 ON g.admin3_grid_id=a3.grid_id
                LEFT JOIN {$tables['grid']} as a4 ON g.admin4_grid_id=a4.grid_id
                LEFT JOIN {$tables['grid']} as a5 ON g.admin5_grid_id=a5.grid_id
                LIMIT 3;" );
$results = mysqli_fetch_all( $query, MYSQLI_ASSOC );

print_r($results);

mysqli_close($con);
print 'End' . PHP_EOL;
