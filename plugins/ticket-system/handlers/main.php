<?
//	register_handler("!^/(\d+)/?$!", "plugins_ticket_system_show_ticket");
//	register_handler("!^/(\d+)/close_ticket/?$!", "plugins_ticket_system_close_ticket");

	hts_data_prehandler("!^(.+/)new_ticket/?$!", array(
			'body' => 'plugins_ticket_system_new_ticket_body',
			'title' => 'Создание нового тикета',
		));

//	register_handler("plugins_ticket_system_main");
	
	function plugins_ticket_system_main($uri, $m)
	{
		echo "********[$uri]:".print_r($m,true)."*******";
		echo "path={$GLOBALS['cms']['plugin_base_path']}<br/>\n";
		echo "uri ={$GLOBALS['cms']['plugin_base_uri']}<br/>\n";
		return true;
	}

	function plugins_ticket_system_show_ticket($uri, $m)
	{
		echo "*11*****[$uri]:".print_r($m,true)."*******";
		echo "path={$GLOBALS['cms']['plugin_base_path']}<br/>\n";
		echo "uri ={$GLOBALS['cms']['plugin_base_uri']}<br/>\n";
		return true;
	}

	function plugins_ticket_system_new_ticket_body($uri, $m)
	{
		exit("xxx");
		require_once('funcs/system.php');
		
		$new_ticket = $GLOBALS['cms']['plugin_base_uri'].get_new_global_id();
		$data = array();

        include_once("funcs/templates/assign.php");
        return template_assign_data("new_ticket.htm", $data);
	}

	function plugins_ticket_system_close_ticket($uri, $m)
	{
		echo "Close ticket; uri='$uri'; m=".print_r($m,true);
		echo "path={$GLOBALS['cms']['plugin_base_path']}<br/>\n";
		echo "uri ={$GLOBALS['cms']['plugin_base_uri']}<br/>\n";
		return true;
	}
?>