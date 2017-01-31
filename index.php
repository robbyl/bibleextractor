<?php

//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);

include './simple_html_dom.php';

$path = realpath('C:/xampp/htdocs/bibleextractor/sw');
$iterator = new RecursiveDirectoryIterator($path);
$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
$objects = new RecursiveIteratorIterator($iterator);

foreach ($objects as $name => $object) {

    echo $name . "<br/>";
    $html = file_get_html($name);
    $some = array();

    foreach ($html->find('span,h3,a,.textAudio,.textHeader,.ym-wbox,.alignCenter,.ym-noprint') as $removed) {
        $removed->outertext = '';
    }

    foreach ($html->find('p', 1) as $element) {

        $some = str_get_html($element->innertext);

        if (!empty($some)) {
            $final = explode(PHP_EOL, trim($some->plaintext));

            for ($index = 0; $index < count($final); $index++) {
                echo ($index + 1) . " " . $final[$index] . "<br/>";
            }
        }
    }

    exit;
}

?>
