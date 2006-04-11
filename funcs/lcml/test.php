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

Добрый день!

Получить работу прочнистом, я так думаю, совершенно реально. Причем в Москве, в НТЦ им. Люльки, который на ул. Касаткина.

А по поводу работы лучше всего обратиться к первоисточнику - в Управления по работе с персоналом sergey.popov@npo-saturn.ru

Насколько известно мне, к молодым спецам в компании отношение хорошее. Кадры там готовят сами и стараются активно привлекать извне. Так что удачи!

=====================
EOT;

//require_once('funcs/lcml/post/00-tables.php');

echo lcml($txt);
echo lcml($txt);
echo lcml($txt);
echo lcml($txt);

?>
