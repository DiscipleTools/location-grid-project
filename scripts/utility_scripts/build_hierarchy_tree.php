<?php

require_once( 'con.php' );

$geonameid = '';




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