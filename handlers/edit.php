<?
    register_action_handler('edit', 'handler_edit');

    function handler_edit($uri, $action)
	{
//		echo "Test edit handler. Action = $action. global = {$GLOBALS['cms']['action']}";

		require_once("funcs/check/access.php");

		if(!check_action_access(4, $uri))
			return true;
		
        $hts  = new DataBaseHTS;

		$GLOBALS['page_data']['source'] = $hts->get_data($uri, 'source') ? '[module admin/edit]' : '[module admin/create-page]';

		show_page($uri);

		return true;
	}
?>
