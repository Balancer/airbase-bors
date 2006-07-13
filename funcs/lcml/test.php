<?

//yyyyyyyyyyyyyyyy

$_SERVER['DOCUMENT_ROOT'] = "/var/www/www.aviaport.ru/htdocs";
$_SERVER['HTTP_HOST'] = "www.aviaport..ru";
$GLOBALS['cms']['page_path'] = $GLOBALS['main_uri'] = "http://www.aviaport.ru/";

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
require_once('funcs/lcml.php');
require_once('funcs/global-data.php');

$GLOBALS['cms']['cache_disabled'] = true;

$txt=<<<EOT
=====================

[url]www.ru[/url]

ВОПРОС О РАЗВИТИИ ОТЕЧЕСТВЕННОГО ВОЗДУХОПЛАВАНИЯ И ДИРИЖАБЛЕСТРОЕНИЯ

<url>http://president.yandex.ru/question.xml?id=145670</url>

ПОДДЕРЖИТЕ ГОЛОСОВАНИЕМ, ЧТОБ ПО РЕЙТИНГУ ВОПРОС БЫЛ ОЗВУЧЕН ВЕРХАМИ И НАЧАЛИСЬ ТЕЛОДВИЖЕНИЯ ВЛАСТИ

=====================
EOT;

//require_once('funcs/lcml/post/00-tables.php');

echo lcml($txt);

?>
