<?
require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
require_once('funcs/lcml.php');
require_once('funcs/global-data.php');

$GLOBALS['cms']['cache_disabled'] = true;

$txt=<<<EOT
=====================
http://balancer.ru/forums/viewtopic.php?pid=34878#p34878
=====================
EOT;

//require_once('funcs/lcml/post/00-tables.php');

echo lcml($txt);

?>
