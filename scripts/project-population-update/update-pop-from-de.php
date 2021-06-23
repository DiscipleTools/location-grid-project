<?php
require_once( 'con.php' );

print 'BEGIN' . PHP_EOL;

switch( $argv[1] ) {
    case 'view':
        view( $con );
        break;
    case 'convert_characters':
        convert_characters( $con );
        break;
    case 'add_admin':
        add_admin( $con );
        break;
    case 'install_admin2':
        install_admin2( $con );
        break;
    default:
        break;
}

function view( $con ) {
    $query_raw = mysqli_query( $con,
        "SELECT * FROM scrapes" );
    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

    $admin1 = '';
    $admin1_name = '';
    foreach( $query as $row ) {
        if ( 'admin1' === $row['level']) {
            $admin1 = $row['level'];
            $admin1_name = $row['name'];
        }

        print $row['wiki_name'] . ' | ' . $row['level'] . ' - ' . $admin1 . ' ' . ic( $admin1_name ) . PHP_EOL;
    }
}

function add_admin( $con ) {
    $query_raw = mysqli_query( $con,
        "SELECT * FROM scrapes" );
    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

    $admin1 = '';
    $admin1_name = '';
    foreach( $query as $row ) {
        if ( 'admin1' === $row['level']) {
            $admin1 = $row['level'];
            $admin1_name = $row['name'];
        }
       $re = mysqli_query( $con,
            "UPDATE scrapes as s
                    SET admin1 = '{$admin1_name}'
                    WHERE wiki_data_id = '{$row['wiki_data_id']}';
                    " );

        print $row['wiki_name'] . ' | ' . $row['level'] . ' - ' . $admin1 . ' ' . ic( $admin1_name ) . PHP_EOL;
    }

}

function convert_characters( $con ) {
    $query_raw = mysqli_query( $con,
        "SELECT * FROM scrapes" );
    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );

    foreach( $query as $row ) {
        $wiki_name = ic( $row['wiki_name'] );
        $name = ic( $row['name'] );
        $admin1_name = ic( $row['admin1'] );

        $re = mysqli_query( $con,
            "UPDATE scrapes as s
                    SET 
                        wiki_name = '{$wiki_name}',
                        name = '{$name}',
                        admin1 = '{$admin1_name}'
                    WHERE wiki_data_id = '{$row['wiki_data_id']}';
                    " );

        print $wiki_name. ' | ' . $row['level'] . ' - ' . ' ' . $admin1_name . PHP_EOL;
    }

}

function ic( $string ) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string);
}

function install_admin1( $con ) {
    $query_raw = mysqli_query( $con,
        "SELECT *
            FROM location_grid
            WHERE level_name = 'admin1'
	            AND country_code = 'AF' 
                " );
    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );


    foreach( $query as $item ){

        $query_raw = mysqli_query( $con,
            "UPDATE location_grid as l
                INNER JOIN (
                    SELECT lg.grid_id as lg_grid_id, lg.wikidata_ref as lg_wikidata_ref, s.wiki_data_id as s_wiki_data_id, lg.name as lg_name, s.name as s_name, s.wiki_name as s_full_name, lg.population as lg_population, s.population as s_population, lg.modification_date as lg_modification_date, lg.population_date as lg_population_date, 		s.date as s_date
                        FROM location_grid lg
                          JOIN scrapes s ON s.name=lg.name AND s.level = 'admin1'
                        WHERE lg.level_name = 'admin1' and s.country LIKE 'Afghanistan%'
                ) as tb
                SET l.population=s_population, l.wikidata_ref = tb.s_wiki_data_id, l.population_date = tb.s_date, l.modification_date = now()
                WHERE l.grid_id = tb.lg_grid_id;
                " );
        if ( empty( $query_raw ) ) {
            print_r( $con );
            die();
        }
    }
}

function install_admin2( $con ) {
    $query_raw = mysqli_query( $con,
        "SELECT grid_id, name, population
            FROM location_grid
            WHERE level_name = 'admin1'
	            AND country_code = 'AF' 
                " );
    if ( empty( $query_raw ) ) {
        print_r( $con );
        die();
    }
    $query = mysqli_fetch_all( $query_raw, MYSQLI_ASSOC );


    foreach( $query as $item ){

        $query_raw = mysqli_query( $con,
            "UPDATE location_grid as l
                INNER JOIN (
                    SELECT lg.grid_id as lg_grid_id, lg.wikidata_ref as lg_wikidata_ref, s.wiki_data_id as s_wiki_data_id, lg.name as lg_name, s.name as s_name, s.wiki_name as s_full_name, lg.population as lg_population, s.population as s_population, lg.modification_date as lg_modification_date, lg.population_date as lg_population_date, 		s.date as s_date
                    FROM location_grid lg
                    JOIN scrapes s ON s.name=lg.name AND s.level = 'admin2' AND s.admin1 = '{$item['name']}'
                    WHERE lg.level_name = 'admin2' AND lg.parent_id = {$item['grid_id']}
                ) as tb
                SET l.population=s_population, l.wikidata_ref = tb.s_wiki_data_id, l.population_date = tb.s_date, l.modification_date = now()
                WHERE l.grid_id = tb.lg_grid_id;
                " );
        if ( empty( $query_raw ) ) {
            print_r( $con );
            die();
        }
    }
}



print 'END' . PHP_EOL;