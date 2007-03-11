<?
	hts_data_prehandler("", array(
			'body'		=> 'balancer_plugins_search_main_body',
			'title'		=> ec('Поиск'),
//			'nav_name'	=> 'balancer_plugins_search_main_nav_name',
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

	function balancer_plugins_search_main_body($uri, $m)
	{
        include_once("funcs/templates/assign.php");
        return template_assign_data("main.html", array());
	}
