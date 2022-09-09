<?php

$list = [];
$row = 1;
if (($handle = fopen("country-levels.csv", "r")) !== FALSE) {
    $data = fgetcsv($handle, 1000, ",");
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $list[] = $data;
//        $num = count($data);
//        print "<p> $num fields in line $row: <br /></p>\n";
//        $row++;
//        for ($c=0; $c < $num; $c++) {
//            print $data[$c] . "<br />\n";
//        }
    }
    fclose($handle);
}



foreach( $list as $item ) {
    print $item[0] . PHP_EOL;
    shell_exec("php build-single-collection.php ".$item[0]. " ". $item[1] . ";");
}
