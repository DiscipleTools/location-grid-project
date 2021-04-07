<?php

$jp_csv = [];
$handle = fopen( __DIR__ . "/jp-people-groups.csv", "r" );
if ( $handle !== false ) {
    while (( $data = fgetcsv( $handle, 0, "," ) ) !== false) {
        $jp_csv_raw[] = $data;
    }
    fclose( $handle );
    foreach( $jp_csv_raw as $row ) {
        $jp_csv[$row[16]] = $row;
    }
    unset($jp_csv['pg_unique_key']);
}

$data = [];
foreach($jp_csv as $row ){
    if ( ! isset( $data[$row[4]] ) ){
        $data[$row[4]] = [
            'name' => '',
            'rop3' => '',
            'locations' => []
        ];
    }

    $data[$row[4]]['name'] = $row[5];
    $data[$row[4]]['rop3'] = $row[4];
    $data[$row[4]]['peopleid3'] = $row[3];
    $data[$row[4]]['locations'][] = [
        'population' => $row[6],
        'lng' => $row[8],
        'lat' => $row[7],
        'level' => $row[9],
        'label' => $row[10],
        'grid_id' => $row[11],
    ];
}


if ( file_exists(__DIR__  . '/jp-people-groups.json')){
    unlink(__DIR__  . '/jp-people-groups.json');
}
file_put_contents( __DIR__  . '/jp-people-groups.json', json_encode($data) );



print count($data) . PHP_EOL;

print 'End ' . PHP_EOL;

