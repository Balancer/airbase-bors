<?
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/templates/smarty.php');

    register_action_handler('edit-data', 'handler_edit_data');

    function handler_edit_data($uri, $action)
	{
		if(!check_action_access(9, $uri))
			return true;

		$GLOBALS['page_data']['source'] = "[module admin/edit-data]";
		
		show_page($uri);
		return true;
	}
?>
