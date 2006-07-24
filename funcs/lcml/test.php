<?

//yyyyyyyyyyyyyyyy

$_SERVER['DOCUMENT_ROOT'] = "/var/www/balancer.ru/htdocs";
$_SERVER['HTTP_HOST'] = "balancer.ru";
$GLOBALS['cms']['page_path'] = $GLOBALS['main_uri'] = "http://balancer.ru/";

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
require_once('funcs/lcml.php');
require_once('funcs/global-data.php');

$GLOBALS['cms']['cache_disabled'] = true;

$txt=<<<EOT
=====================

[COLOR=red]
[FONT=Arial](c) Ааа. Цурупяне!!! Будем жить...[/FONT]
[/COLOR]

[white]csdcsdc[/white]

=====================
EOT;

//require_once('funcs/lcml/post/00-tables.php');

echo lcml($txt, array(
				'cr_type' => 'save_cr',
				'forum_type' => 'punbb',
				'forum_base_uri' => 'http://balancer.ru/forum',
				'sharp_not_comment' => true,
				'html_disable' => true,
			));

?>
