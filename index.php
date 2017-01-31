<?php

//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);


class MyDB extends SQLite3 {

    function __construct() {
        $this->open('mysqlitedb.db');
    }

}

$db = new MyDB();
$db->exec("DROP TABLE verses");
$sql = "CREATE TABLE `verses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `bookId` INTEGER NULL,
  `chapterId` INTEGER NULL,
  `verse` INTEGER NULL,
  `text` VARCHAR NULL)";

$db->exec($sql);

//exit;

include './simple_html_dom.php';

$path = realpath('C:/xampp/htdocs/bibleextractor/sw');
$iterator = new RecursiveDirectoryIterator($path);
$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
$objects = new RecursiveIteratorIterator($iterator);

foreach ($objects as $name) {

    echo $name . "<br/>";
    
    $exploded = explode('\\', $name);
    
    $book = $exploded[5];
    $chapter_explode = explode(".", $exploded[6]);
    $chapter = $chapter_explode[0];
    
    
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
//                $key = $index + 1;
//                $part .= "({$book}, {$chapter}, {$key}, '{$final[$index]}'),";
            }
        }
    }

    echo '<br/>';
//    $build_query = "INSERT INTO verses (bookId, chapterId, verse, text) VALUES " . substr(trim($part), 0, -1);
//    $db->exec($build_query);
//    echo $build_query;

//    exit;
}
?>
