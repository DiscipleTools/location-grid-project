<?php

require_once( 'con.php' );

//SELECT g.admin0_code as code, ( SELECT name FROM location_grid as lg WHERE lg.admin0_code = g.admin0_code AND level = 'admin0' LIMIT 1) as name, count(g.grid_id) as count FROM location_grid as g GROUP BY g.admin0_code ORDER BY name;