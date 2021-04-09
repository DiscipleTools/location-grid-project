<?php
/**
 * NOTES
 * This script builds all the points for the country level maps.
 *
 * Creates:
 * census.txt
 * missing.txt
 * missing.csv
 *
 */
require_once( 'con.php' );

/** FOLDERS */
if ( ! file_exists( $folders['root'] ) ) {
    print date('H:i:s') . ' | Folder Not Found. ' . $folders['root'] . PHP_EOL;
    exit;
}
$folder['polygon_collection'] = $folders['root'] . 'polygon_collection/';
$folder['polygon'] = $folders['root'] . 'single_polygons/';
$folder['points'] = $folders['root'] . 'points/';
/** END FOLDERS*/


/** TOTALS */
$totals = [];
$totals['counts'] = [
    'All Rows' => 0,
    'Country' => 0,
    'Admin1' => 0,
    'Admin2' => 0,
    'Admin3' => 0,
];
$totals['polygons'] = [
    'Countries' => 0,
    'States' => 0,
];
$totals['single_polygons'] = [
    'Countries' => 0,
    'States' => 0,
    'Counties' => 0,
];
$totals['points'] = [
    'Countries' => 0,
    'States' => 0,
];
$totals['population'] = [
    'Total' => 0,
    'Countries' => 0,
    'States' => 0,
    'Counties' => 0,
];
$totals['bbox'] = [
    'Countries' => 0,
    'States' => 0,
    'Counties' => 0,
];

$missing = [];
$missing['polygons'] = [
    'Countries' => 0,
    'States' => 0,
];
$missing['single_polygons'] = [
    'Countries' => 0,
    'States' => 0,
    'Counties' => 0,
];
$missing['points'] = [
    'Countries' => 0,
    'States' => 0,
];
$missing['population'] = [
    'Total' => 0,
    'Countries' => 0,
    'States' => 0,
    'Counties' => 0,
];
$missing['bbox'] = [
    'Countries' => 0,
    'States' => 0,
    'Counties' => 0,
];
/** END TOTALS */


/** QUERIES */
$total_raw = mysqli_query( $con, "
        SELECT ('All Rows') as level, count(*) as count
        FROM {$tables['geonames']} as g
    " );
$total = mysqli_fetch_all($total_raw, MYSQLI_ASSOC);

$counts_results = mysqli_query( $con, "
        SELECT g.level, count(g.level) as count
        FROM {$tables['geonames']} as g
        GROUP BY g.level;
    " );
$counts = mysqli_fetch_all($counts_results, MYSQLI_ASSOC);

$country_results = mysqli_query( $con, "
        SELECT g.geonameid, g.level, g.name
        FROM {$tables['geonames']} as g
        WHERE g.level = 'country';
    " );
$countries = mysqli_fetch_all($country_results, MYSQLI_ASSOC);

$admin1_results = mysqli_query( $con, "
        SELECT g.geonameid, g.name, g.level, c.name as country
        FROM {$tables['geonames']} as g
        LEFT JOIN  {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
        WHERE g.level = 'admin1';
    " );
$states = mysqli_fetch_all($admin1_results, MYSQLI_ASSOC);

$admin2_results = mysqli_query( $con, "
        SELECT g.geonameid, g.name, g.level, c.name as country
        FROM {$tables['geonames']} as g
        LEFT JOIN  {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
        WHERE g.level = 'admin2';
    " );
$counties = mysqli_fetch_all($admin2_results, MYSQLI_ASSOC);

$population_results = mysqli_query( $con, "
        SELECT g.geonameid, g.level, g.name, g.population, c.name as country
        FROM {$tables['geonames']} as g
        LEFT JOIN  {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
        WHERE g.population = 0;
    " );
$missing_population = mysqli_fetch_all($population_results, MYSQLI_ASSOC);

$population_results = mysqli_query( $con, "
        SELECT g.geonameid, g.level, g.name, g.population, c.name as country
        FROM {$tables['geonames']} as g
        LEFT JOIN  {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
        WHERE g.population != 0;
    " );
$found_population = mysqli_fetch_all($population_results, MYSQLI_ASSOC);

$bbox_results = mysqli_query( $con, "
        SELECT g.geonameid, g.level, g.name, c.name as country
        FROM {$tables['geonames']} as g
        LEFT JOIN  {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
        WHERE g.north_latitude IS NULL;
    " );
$missing_bbox = mysqli_fetch_all($bbox_results, MYSQLI_ASSOC);

$bbox_results = mysqli_query( $con, "
        SELECT g.geonameid, g.level, g.name, c.name as country
        FROM {$tables['geonames']} as g
        LEFT JOIN  {$tables['geonames']} as c ON g.country_geonameid=c.geonameid
        WHERE g.north_latitude IS NOT NULL;
    " );
$found_bbox = mysqli_fetch_all($bbox_results, MYSQLI_ASSOC);

$hierarchy_results = mysqli_query( $con, "
        SELECT g.geonameid, g.name, g.feature_code, g.country_code
        FROM {$tables['geonames']} as g
        WHERE g.parent_id IS NULL
        ORDER BY g.name ASC;
    " );
$missing_hierarchy = mysqli_fetch_all($hierarchy_results, MYSQLI_ASSOC);
/** END QUERIES */


/** BUFFER CENSUS OUTPUT */
ob_start();

print '
**********************************************************************
*                                                                    *              
*    CENSUS FOR SATURATION GRID PROJECT                              *
*                                                                    *
**********************************************************************
' . PHP_EOL. PHP_EOL;

print 'Geonames Data Source Totals:' . PHP_EOL;
print '**************************************' . PHP_EOL;
$order =[];
foreach ( $counts as $row ) {
    if ( ! empty( $row['level'] ) ) {
        $level = ucwords( $row['level']);
        $totals['counts'][$level] = $row;
    }
}
$totals['counts']['All Rows'] = $total[0];
foreach ( $totals['counts'] as $row ) {
    print ucwords( $row['level'] ) . ' : ' . $row['count'] . PHP_EOL;
}


print PHP_EOL. PHP_EOL . 'Complete:' . PHP_EOL;
print '**************************************' . PHP_EOL;

print PHP_EOL . '-- Polygons Folder --' . PHP_EOL;
foreach( $countries as $country ) {
    if ( file_exists( $folder['polygon_collection'] . $country['geonameid'] .  '.geojson') ) {
        $totals['polygons']['Countries']++;
    }
}
foreach( $states as $admin1 ) {
    if ( file_exists( $folder['polygon_collection'] . $admin1['geonameid'] .  '.geojson') ) {
        $totals['polygons']['States']++;
    }
}
foreach ( $totals['polygons'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}


print PHP_EOL . '-- Single Polygons Folder --' . PHP_EOL;
foreach( $countries as $country ) {
    if ( file_exists( $folder['polygon'] . $country['geonameid'] .  '.geojson') ) {
        $totals['single_polygons']['Countries']++;
    }
}
foreach( $states as $admin1 ) {
    if ( file_exists( $folder['polygon'] . $admin1['geonameid'] .  '.geojson') ) {
        $totals['single_polygons']['States']++;
    }
}
foreach( $counties as $admin2 ) {
    if ( file_exists( $folder['polygon'] . $admin2['geonameid'] .  '.geojson') ) {
        $totals['single_polygons']['Counties']++;
    }
}
foreach ( $totals['single_polygons'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}


print PHP_EOL . '-- Points Folder --' . PHP_EOL;
foreach( $countries as $country ) {
    if ( file_exists( $folder['points'] . $country['geonameid'] .  '.geojson') ) {
        $totals['points']['Countries']++;
    }
}
foreach( $states as $admin1 ) {
    if ( file_exists( $folder['points'] . $admin1['geonameid'] .  '.geojson') ) {
        $totals['points']['States']++;
    }
}
foreach ( $totals['points'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}

print PHP_EOL . '-- Populations in DB --' . PHP_EOL;
foreach( $found_population as $population ) {
    $totals['population']['Total']++;
}
foreach( $found_population as $population ) {
    if ( $population['level'] === 'country' ) {
        $totals['population']['Countries']++;
    }
}
foreach( $found_population as $population ) {
    if ( $population['level'] === 'admin1' ) {
        $totals['population']['States']++;
    }
}
foreach( $found_population as $population ) {
    if ( $population['level'] === 'admin2' ) {
        $totals['population']['Counties']++;
    }
}
foreach ( $totals['population'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}

print PHP_EOL . '-- BBox Info in DB --' . PHP_EOL;
foreach( $found_bbox as $value ) {
    if ( $value['level'] === 'country' ) {
        $totals['bbox']['Countries']++;
    }
}
foreach( $found_bbox as $value ) {
    if ( $value['level'] === 'admin1' ) {
        $totals['bbox']['States']++;
    }
}
foreach( $found_bbox as $value ) {
    if ( $value['level'] === 'admin2' ) {
        $totals['bbox']['Counties']++;
    }
}
foreach ( $totals['bbox'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}



print PHP_EOL. PHP_EOL .PHP_EOL. PHP_EOL . 'Missing:' . PHP_EOL;
print '**************************************' . PHP_EOL ;

print PHP_EOL . '-- Polygons Folder --' . PHP_EOL;
foreach( $states as $admin1 ) {
    if ( ! file_exists( $folder['polygon_collection'] . $admin1['geonameid'] .  '.geojson') ) {
        $missing['polygons']['States']++;
    }
}
foreach( $countries as $country ) {
    if ( ! file_exists( $folder['polygon_collection'] . $country['geonameid'] .  '.geojson') ) {
        $missing['polygons']['Countries']++;
    }
}
foreach ( $missing['polygons'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}


print PHP_EOL . '-- Single Polygons Folder --' . PHP_EOL;
foreach( $counties as $admin2 ) {
    if ( ! file_exists( $folder['polygon'] . $admin2['geonameid'] .  '.geojson') ) {
        $missing['single_polygons']['Counties']++;
    }
}
foreach( $states as $admin1 ) {
    if ( ! file_exists( $folder['polygon'] . $admin1['geonameid'] .  '.geojson') ) {
        $missing['single_polygons']['States']++;
    }
}
foreach( $countries as $country ) {
    if ( ! file_exists( $folder['polygon'] . $country['geonameid'] .  '.geojson') ) {
        $missing['single_polygons']['Countries']++;
    }
}
foreach ( $missing['single_polygons'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}


print PHP_EOL . '-- Points Folder --' . PHP_EOL;
foreach( $states as $admin1 ) {
    if ( ! file_exists( $folder['points'] . $admin1['geonameid'] .  '.geojson') ) {
        $missing['points']['States']++;
    }
}
foreach( $countries as $country ) {
    if ( ! file_exists( $folder['points'] . $country['geonameid'] .  '.geojson') ) {
        $missing['points']['Countries']++;
    }
}
foreach ( $missing['points'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}


print PHP_EOL . '-- Populations in DB --' . PHP_EOL;
foreach( $missing_population as $population ) {
    $missing['population']['Total']++;
}
foreach( $missing_population as $population ) {
    if ( $population['level'] === 'country' ) {
        $missing['population']['Countries']++;
    }
}
foreach( $missing_population as $population ) {
    if ( $population['level'] === 'admin1' ) {
        $missing['population']['States']++;
    }
}
foreach( $missing_population as $population ) {
    if ( $population['level'] === 'admin2' ) {
        $missing['population']['Counties']++;
    }
}
foreach ( $missing['population'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}


print PHP_EOL . '-- BBox Info in DB --' . PHP_EOL;
foreach( $missing_bbox as $population ) {
    $missing['population']['Total']++;
}
foreach( $missing_bbox as $value ) {
    if ( $value['level'] === 'country' ) {
        $missing['bbox']['Countries']++;
    }
}
foreach( $missing_bbox as $value ) {
    if ( $value['level'] === 'admin1' ) {
        $missing['bbox']['States']++;
    }
}
foreach( $missing_bbox as $value ) {
    if ( $value['level'] === 'admin2' ) {
        $missing['bbox']['Counties']++;
    }
}
foreach ( $missing['bbox'] as $key => $total ) {
    print ucwords( $key ) . ' : ' . $total . PHP_EOL;
}










print PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . date('Y-m-d H:i:s') . PHP_EOL;
/** END BUFFER CENSUS OUTPUT */

/** CREATE CENSUS FILE */
$content = ob_get_contents();
file_put_contents( $folders['root'] . 'totals.txt', $content ); // put copy in saturation folder
file_put_contents( $output['files'] . 'totals.txt', $content ); // put copy in output folder
ob_end_clean();
/** END CREATE CENSUS FILE */






/** BEGIN MISSING LOCATIONS TXT OUTPUT */
ob_start();
print PHP_EOL . '
MISSING LOCATIONS REPORT 
' . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

print PHP_EOL . '
**********************************************************************
*                                                                    *
*                                                                    *
*             POLYGONS: COUNTRIES                                    *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL . PHP_EOL;
foreach( $countries as $country ) {
    if ( ! file_exists( $folder['polygon_collection'] . $country['geonameid'] .  '.geojson') ) {
        print '(' . $country['geonameid'] . ') ' . $country['name']  . PHP_EOL;
    }
}

print PHP_EOL . '
**********************************************************************
*                                                                    *
*                                                                    *
*             POLYGONS: STATES (ADMIN1)                              *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL . PHP_EOL;
foreach( $states as $admin1 ) {
    if ( ! file_exists( $folder['polygon_collection'] . $admin1['geonameid'] .  '.geojson') ) {
        print '(' . $admin1['geonameid'] . ') ' . $admin1['name']. ', ' . $admin1['country'] . PHP_EOL;
    }
}


// Single Polygons
print PHP_EOL . '
**********************************************************************
*                                                                    *
*                                                                    *
*             SINGLE POLYGONS: COUNTRIES                             *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL . PHP_EOL;
foreach( $countries as $country ) {
    if ( ! file_exists( $folder['polygon'] . $country['geonameid'] .  '.geojson') ) {
        print '(' . $country['geonameid'] . ') ' . $country['name']  . PHP_EOL;
    }
}
print PHP_EOL . '
**********************************************************************
*                                                                    *
*                                                                    *
*             SINGLE POLYGONS: STATES (ADMIN1)                       *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL . PHP_EOL;
foreach( $states as $admin1 ) {
    if ( ! file_exists( $folder['polygon'] . $admin1['geonameid'] .  '.geojson') ) {
        print '(' . $admin1['geonameid'] . ') ' . $admin1['name']. ', ' . $admin1['country'] . PHP_EOL;
    }
}
print PHP_EOL . '
**********************************************************************
*                                                                    *
*                                                                    *
*             SINGLE POLYGONS: COUNTIES (ADMIN2)                     *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL . PHP_EOL;
foreach( $counties as $admin2 ) {
    if ( ! file_exists( $folder['polygon'] . $admin2['geonameid'] .  '.geojson') ) {
        print '(' . $admin2['geonameid'] . ') ' . $admin2['name']. ', ' . $admin2['country'] . PHP_EOL;
    }
}
/** END MISSING LOCATIONS TXT OUTPUT */

/** CREATE FILE MISSING LOCATIONS TXT */
$content = ob_get_contents();
file_put_contents( $output['files'] . 'missing_polygons.txt', $content ); // put copy in output folder
file_put_contents( $folders['root'] . 'missing_polygons.txt', $content ); // put copy in saturation folder
ob_end_clean();
/** END SAVE MISSING LOCATIONS TXT */



/** CREATE MISSING STATES (ADMIN1) POLYGONS CSV */
ob_start();
foreach( $states as $admin1 ) {
    if ( ! file_exists( $folder['polygon'] . $admin1['geonameid'] .  '.geojson') ) {
        print $admin1['geonameid'] . ', "' . $admin1['name']. '","' . $admin1['country'] . '","' . $admin1['name']. ' ' . $admin1['country'] . '",""' . PHP_EOL;
    }
}
$content = ob_get_contents();
file_put_contents( $output['files'] . 'missing_admin1_polygons.csv', $content );
file_put_contents( $folders['root'] . 'scripts/missing_admin1_polygons.csv', $content );
ob_end_clean();
/** END MISSING STATES POLYGONS */


/** CREATE MISSING COUNTIES (ADMIN2) POLYGONS CSV */
ob_start();
foreach( $counties as $admin2 ) {
    if ( ! file_exists( $folder['polygon'] . $admin2['geonameid'] .  '.geojson') ) {
        print $admin2['geonameid'] . ', "' . $admin2['name']. '","' . $admin2['country'] . '","' . $admin2['name']. ' ' . $admin2['country'] . '",""' . PHP_EOL;
    }
}
$content = ob_get_contents();
file_put_contents( $output['files'] . 'missing_admin2_polygons.csv', $content );
file_put_contents( $folders['root'] . 'scripts/missing_admin2_polygons.csv', $content );
ob_end_clean();
/** END MISSING COUNTIES POLYGONS */



/** CREATE MISSING POPULATIONS CSV */
ob_start();
foreach( $missing_population as $value ) {
    print $value['geonameid'] . ', "' . $value['name']. '","' . $value['country'] . '","' . $value['name']. ' ' . $value['country'] . '",'. $value['population'] . PHP_EOL;
}
$content = ob_get_contents();
file_put_contents( $output['files'] . 'missing_populations.csv', $content );
file_put_contents( $folders['root'] . 'scripts/missing_populations.csv', $content );
ob_end_clean();
/** END MISSING COUNTIES POLYGONS */

/** CREATE MISSING POPULATIONS TXT */
ob_start();
print '
**********************************************************************
*                                                                    *
*                                                                    *
*                       MISSING POPULATIONS                          *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL . PHP_EOL;
print date('Y-m-d H:i:s');
$array = [];
foreach( $missing_population as $value ) {
    $array[$value['country']][] = '('. $value['geonameid'] . ' - ' . $value['level'] .') ' . $value['name'] ;
}
ksort( $array );
unset($array['']);
foreach( $array as $key => $value ) {
    print PHP_EOL . PHP_EOL . ucwords( $key ) . PHP_EOL;
    foreach ( $value as $item ) {
        print '--- ' . $item . PHP_EOL;
    }
}
$content = ob_get_contents();
file_put_contents( $output['files'] . 'missing_populations.txt', $content );
file_put_contents( $folders['root'] . 'missing_populations.txt', $content );
ob_end_clean();
/** END MISSING COUNTIES POLYGONS */


/** CREATE MISSING POPULATIONS TXT */
ob_start();
print '
**********************************************************************
*                                                                    *
*                                                                    *
*                       MISSING BOUNDING BOX INFO                    *   
*                                                                    *
*                                                                    *
**********************************************************************
This refers to the north_latitude,south_latitude,west_longitude,east_logitude columns.
' . PHP_EOL . PHP_EOL;
print date('Y-m-d H:i:s');
$array = [];
foreach( $missing_bbox as $value ) {
    $array[$value['country']][] = '('. $value['geonameid'] . ' - ' . $value['level'] .') ' . $value['name'] ;
}
ksort( $array );
unset($array['']);
foreach( $array as $key => $value ) {
    print PHP_EOL . PHP_EOL . ucwords( $key ) . PHP_EOL;
    foreach ( $value as $item ) {
        print '--- ' . $item . PHP_EOL;
    }
}
$content = ob_get_contents();
file_put_contents( $output['files'] . 'missing_boundingbox_info.txt', $content );
file_put_contents( $folders['root'] . 'missing_boundingbox_info.txt', $content );
ob_end_clean();
/** END MISSING COUNTIES POLYGONS */


/** CREATE MISSING POPULATIONS TXT */
ob_start();
print '
**********************************************************************
*                                                                    *
*                                                                    *
*                       MISSING HIERARCHY                            *   
*                                                                    *
*                                                                    *
**********************************************************************
' . PHP_EOL . PHP_EOL;
print date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
$array = [];
foreach( $missing_hierarchy as $value ) {
    print  '('. $value['geonameid'] . ') ' . $value['name'] . PHP_EOL;
}
$content = ob_get_contents();
file_put_contents( $output['files'] . 'missing_hierarchy.txt', $content );
file_put_contents( $folders['root'] . 'missing_hierarchy.txt', $content );
ob_end_clean();
/** END MISSING COUNTIES POLYGONS */









/*******************************************************************************************************
 *
 * Build Hierarchy Files
 *
 *******************************************************************************************************/

// build list array
$response = [];

// pre-start list
$start_geonameid = 6295630;
$parent_object = mysqli_query( $con, "
        SELECT parent_id, geonameid as id, name 
          FROM {$tables['geonames']} 
          WHERE parent_id = {$start_geonameid} 
          ORDER BY name ASC;
    " );
$parent = mysqli_fetch_all($parent_object, MYSQLI_ASSOC);
$response['list'] = $parent;

// build full results
$all_object = mysqli_query( $con, "
        SELECT parent_id, geonameid as id, name 
        FROM {$tables['geonames']};
    " );
$all = mysqli_fetch_all($all_object, MYSQLI_ASSOC);

if ( empty( $all ) ) {
    return _no_results();
}
$menu_data = prepare_menu_array( $all );
$response['text'] = build_locations_html_list( $start_geonameid, $menu_data, 0, 10 );

function build_locations_html_list( $parent_id, $menu_data, $gen, $depth_limit ) {
    $list = '';

    if ( isset( $menu_data['parents'][$parent_id] ) && $gen < $depth_limit ) {
        $gen++;
        foreach ($menu_data['parents'][$parent_id] as $item_id)
        {
            switch( $gen ) {
                case '0':
                    $list .= '';
                    break;
                case '1':
                    $list .= '';
                    break;
                case '2':
                    $list .= '';
                    break;
                case '3':
                    $list .= '---- ';
                    break;
                case '4':
                    $list .= '---- ---- ';
                    break;
            }
            if ( $gen === 2 ) {
                $list .= PHP_EOL;
            }
            $list .= $menu_data['items'][ $item_id ]['name'] . ' (' . $item_id . ')' . PHP_EOL;
            if ( $gen === 1 ) {
                $list .= PHP_EOL;
            }
            $sub = build_locations_html_list( $item_id, $menu_data, $gen, $depth_limit );
            if ( ! empty( $sub ) ) {
                $list .= $sub;
            }
        }
    }
    return $list;
}
function prepare_menu_array( $query) {
    // prepare special array with parent-child relations
    $menu_data = array(
        'items' => array(),
        'parents' => array()
    );

    foreach ( $query as $menu_item )
    {
        $menu_data['items'][$menu_item['id']] = $menu_item;
        $menu_data['parents'][$menu_item['parent_id']][] = $menu_item['id'];
    }
    return $menu_data;
}
function _no_results() {
    return '<p>'. esc_attr( 'No Results', 'disciple_tools' ) .'</p>';
}


file_put_contents( $output['files'] . 'hierarchy.txt', $response['text'] );
file_put_contents( $folders['root'] . 'hierarchy.txt', $response['text'] );


// Close connections and buffering
mysqli_close($con);
