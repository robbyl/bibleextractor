
<form action="" method="post">
    <p>Import books <button name="books" value="books">Upload</button></p>
    <p>Import verses <button name="verses" value="verses">Upload</button></p>

</form>

<?php
error_reporting(E_ALL & ~E_NOTICE);
include './simple_html_dom.php';

$db = new MyDB();

if (!empty($_POST['books'])) {

    $db->exec("DROP TABLE IF EXISTS key_english");
    $sql = 'CREATE TABLE "key_english" (
  "b" integer PRIMARY KEY AUTOINCREMENT  NOT NULL,
  "n" text NOT NULL
)';

    $verseQ = "";
    $db->exec($sql);

    echo "Importing books <br/>";

    $html = file_get_html("C:/xampp/htdocs/biblesw/index.htm");

    foreach ($html->find('ul') as $ul) {
        foreach ($ul->find('a') as $li) {
            $wacha = str_get_html($li);

            $verseQ = $verseQ . "('{$wacha->plaintext}'),";
        }
    }

    $build_books = "INSERT INTO key_english (n) VALUES " . substr(trim($verseQ), 0, -1);
    
    $db->exec($build_books);

    echo "Import books finished";
}

if (!empty($_POST['verses'])) {

    $db->exec("DROP TABLE IF EXISTS t_sw");
    $sql = 'CREATE TABLE "t_sw" (
  "id" integer zerofill PRIMARY KEY NOT NULL,
  "b" integer NOT NULL,
  "c" integer NOT NULL,
  "v" integer NOT NULL,
  "t" text NOT NULL
)';

    $db->exec($sql);

    echo "Import verses to SQLite db <br/>";
    
    $path = realpath('C:/xampp/htdocs/biblesw/sw');
$iterator = new RecursiveDirectoryIterator($path);
$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
$objects = new RecursiveIteratorIterator($iterator);

$part = "";

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
    
    
    echo "Importing verses finished";
}

class MyDB extends SQLite3 {

    function __construct() {
        $this->open('bible_sw.db');
    }

}
?>
