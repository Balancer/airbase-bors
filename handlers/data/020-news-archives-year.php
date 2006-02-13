<?
	$regex = "!^(http://.+/news/)(\d+)/?$!";
	hts_data_prehandler_add($regex, 'body', 	"news_archives_year_get_body");
	hts_data_prehandler_add($regex, 'source',	create_function('$uri, $m', 'return ec("Это виртуальная страница! Не сохраняйте значение.");'));
	hts_data_prehandler_add($regex, 'title',	create_function('$uri, $m', 'return $m[2];'));
	hts_data_prehandler_add($regex, 'nav_name',	create_function('$uri, $m', 'return $m[2];'));
	hts_data_prehandler_add($regex, 'parent',	create_function('$uri, $m', 'return array($m[1]);'));

    function news_archives_year_get_body($uri, $m=array())
	{
		return ec("Архив. Заглушка.");
    }
?>
