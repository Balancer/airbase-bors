<?
    register_action('edit', 'handler_edit');

    function handler_edit($uri, $action)
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(3, $uri))
			return true;
		
        $hts  = &new DataBaseHTS($uri);

		$data = array(
			'source' => $hts->get('source'),
		);

		include_once("engines/smarty/assign.php");
		$GLOBALS['page_data']['body'] = template_assign_data("edit.html", $data);

		show_page($uri);

		return true;
	}
