<?php

error_reporting(E_ALL & ~E_NOTICE);

include './simple_html_dom.php';

$path = realpath('C:/xampp/htdocs/biblesw/sw');
$iterator = new RecursiveDirectoryIterator($path);
$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
$objects = new RecursiveIteratorIterator($iterator);

foreach ($objects as $name => $object) {
    
    echo $name . "<br/>";
    $html = file_get_html($name);
    $some = array();
    foreach ($html->find('p', 1) as $element) {

        $some = explode(PHP_EOL, $element->plaintext);

        for ($i = 0; $i < count($some); $i++) {
//            echo preg_replace('/^([0-9]* \w+ )?(.*)$/', '$2', $some[$i]) . '<br/>';
            echo $some[$i];
            echo '<br/>';
        }
    }
}


//
//$html = file_get_html('C:\xampp\htdocs\biblesw\sw\01\1.htm');
//
//// Find all images 
//$some = array();
//foreach ($html->find('p', 1) as $element) {
//
//  $some =  explode(PHP_EOL, $element->plaintext);
//  
//  for($i = 0; $i < count($some); $i++){
//      echo preg_replace('/^([0-9]* \w+ )?(.*)$/', '$2',$some[$i]) . '<br/>';
//  }
//}
?>
