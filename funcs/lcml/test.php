<?
ini_set('default_charset','windows-1251');
setlocale(LC_ALL, "ru_RU.cp1251");
$DOCUMENT_ROOT="/home/airbase/html";

require_once('../lcml.php');

require_once('funcs/global-data.php');

$txt=<<<EOT
=====================
[url test|[url test2|test3]]
1
2
3
=====================
* 11111111
* 22222222
=====================
EOT;

//require_once('funcs/lcml/post/00-tables.php');

echo lcml($txt);

?>
