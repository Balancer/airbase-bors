<?
	$regex = "!(http://.+/news/)(\d{4})/(\d{2})/?$!";

	hts_data_prehandler_add($regex, 'body', 	"news_archives_month_get_body");
	hts_data_prehandler_add($regex, 'source',	create_function('$uri, $m', 'return ec("Это виртуальная страница! Не сохраняйте значение.");'));
	hts_data_prehandler_add($regex, 'title',	create_function('$uri, $m', 'include_once("funcs/datetime.php"); return month_name($m[3])." ".$m[2].ec(" года");'));
	hts_data_prehandler_add($regex, 'nav_name',	create_function('$uri, $m', 'include_once("funcs/datetime.php"); return month_name($m[3]);'));
	hts_data_prehandler_add($regex, 'parent',	create_function('$uri, $m', 'return array("{$m[1]}{$m[2]}/");'));

    function news_archives_month_get_body($uri, $m=array())
	{
		return ec("Архив. Заглушка.");
    }
?>
