<?

//yyyyyyyyyyyyyyyy

$_SERVER['DOCUMENT_ROOT'] = "/var/www/airbase.ru/htdocs";
$_SERVER['HTTP_HOST'] = "airbase.ru";
$GLOBALS['cms']['page_path'] = $GLOBALS['main_uri'] = "http://airbase.ru/";

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
require_once('funcs/lcml.php');
require_once('funcs/global-data.php');

$GLOBALS['cms']['cache_disabled'] = true;

$txt=<<<EOT
=====================
[url=http://sigs.ru][img]http://sigs.ru/la2/9/male/spellhowler/Boyarik/50/17/Dark Elf/i.jpg[/img][/url]
=====================
EOT;

//require_once('funcs/lcml/post/00-tables.php');

echo lcml($txt);

?>
