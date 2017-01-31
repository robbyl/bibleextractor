<?php

error_reporting(E_ALL & ~E_NOTICE);

class MyDB extends SQLite3 {

    function __construct() {
        $this->open('mysqlitedb.db');
    }

}

$db = new MyDB();
$db->exec("DROP TABLE IF EXISTS t_sw");
$sql = 'CREATE TABLE "t_sw" (
  "id" integer zerofill PRIMARY KEY NOT NULL,
  "b" integer NOT NULL,
  "c" integer NOT NULL,
  "v" integer NOT NULL,
  "t" text NOT NULL
)';

$db->exec($sql);

include './simple_html_dom.php';

$path = realpath('C:/xampp/htdocs/biblesw/sw');
$iterator = new RecursiveDirectoryIterator($path);
$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
$objects = new RecursiveIteratorIterator($iterator);

$part = "";

echo "Importing to sqlite";

foreach ($objects as $name) {

//    echo $name . "<br/>";

    $exploded = explode('\\', $name);

    $book = $exploded[5];
    $chapter_explode = explode(".", $exploded[6]);
    $chapter = $chapter_explode[0];

    $html = file_get_html($name);

    foreach ($html->find('span,h3,a,.textAudio,.textHeader,.ym-wbox,.alignCenter,.ym-noprint') as $removed) {
        $removed->outertext = '';
    }

    foreach ($html->find('p', 1) as $element) {

        $some = str_get_html($element->innertext);

        if (!empty($some)) {
            $final = explode(PHP_EOL, trim($some->plaintext));

            for ($index = 0; $index < count($final); $index++) {
//                echo ($index + 1) . " " . $final[$index] . "<br/>";
                $key = $index + 1;
                $book1 = str_pad($book, 2, "0", STR_PAD_LEFT);
                $chapter1 = str_pad($chapter, 3, "0", STR_PAD_LEFT);
                $key1 = str_pad($key, 3, "0", STR_PAD_LEFT);

                $part = $part . "({$book1}{$chapter1}{$key1},{$book}, {$chapter}, {$key}, \"{$final[$index]}\"),";
            }
        }
        ;
    }

//    exit;
}

$build_query = "INSERT INTO t_sw (id, b, c, v, t) VALUES " . substr(trim($part), 0, -1);
$db->exec($build_query);

echo "Finished importing";
?>
