<?
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/templates/smarty.php');

	if(empty($GLOBALS['cms']['only_load']))
	    register_handler('!^(http://[^/]+.*)/$!', 'handler_new_page');

    function handler_new_page($uri, $m=array())
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(3, $uri))
			return false;

        $hts  = &new DataBaseHTS($uri);

		$data = array(
			'source' => $hts->get('source'),
			'title'  => $title = $hts->get('title'),
		);

		include_once("engines/smarty/assign.php");
		$data = array(
			'body' => template_assign_data("new-page.html", $data),
			'title' => $title ? $title : ec('Создание новой страницы'),
		);

		show_page($uri, $data);

		return true;
	}
