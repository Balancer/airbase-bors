<?
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/templates/smarty.php');

    register_uri_handler('!^(http://[^/]+.*)/$!', 'handler_new_page');

    function handler_new_page($uri, $m=array())
	{
		$GLOBALS['page_data']['source'] = '[module admin/create-page]';
		$GLOBALS['cms']['action'] = 'create-page';
    
		show_page($uri);
		return true;
	}
?>
