<?
    register_action_handler('nav_edit', 'handler_edit_nav');
    register_action_handler('nav_child_add', 'handler_edit_nav_child_add');
    register_action_handler('nav_child_delete', 'handler_edit_nav_child_delete');
    register_action_handler('nav_parent_add', 'handler_edit_nav_parent_add');
    register_action_handler('nav_parent_delete', 'handler_edit_nav_parent_delete');

	function load_icons()
	{
		$tools = array('delete' => '<img src="http://www.aviaport.ru/images/tools/b_drop.png" width="16" height="16" border="0" alt="del" title="'.ec("Удалить").'">');

		return array('tools' => $tools);
	}

    function handler_edit_nav($uri, $action)
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(3, $uri))
			return true;

		$hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);

		$data     = array();
		$children = array();
		$parents  = array();

//		$GLOBALS['log_level'] = 10;

		foreach($hts->dbh->get_array("
				SELECT
					c.value as uri,
					t.value as title
				FROM hts_data_child c
					LEFT JOIN hts_data_title t ON t.id = c.value
				WHERE c.id like '".addslashes($uri)."'
				ORDER BY t.value", false) as $p)
			$children[] = $p;

		foreach($hts->dbh->get_array("
				SELECT
					p.value as uri,
					t.value as title
				FROM hts_data_parent p
					LEFT JOIN hts_data_title t ON t.id = p.value
				WHERE p.id like '".addslashes($uri)."'
				ORDER BY t.value", false) as $p)
			$parents[] = $p;

		$data['parents']  = $parents;
		$data['children'] = $children;

		$data['icons'] = load_icons('tools');

//		print_r($data);

		include_once("funcs/templates/assign.php");
		$data = array(
			'body'  =>  template_assign_data("xfile:".dirname(__FILE__)."/edit-nav.htm", $data),
			'title' => ec("Редактирование навигации страницы ").$hts->get_data($uri, 'title'),
			);
		include_once("funcs/templates/show.php");
		template_assign_and_show($uri, $data);
		return true;
	}

    function handler_edit_nav_child_add($uri, $action)
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(3, $uri))
			return true;

		$hts = new DataBaseHTS;
		
		if(isset($_POST['link']))
			$hts->add_child($uri, $_POST['link']);

		go("$uri?nav_edit");
	}

    function handler_edit_nav_child_delete($uri, $action)
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(3, $uri))
			return true;

		$hts = new DataBaseHTS;
		
		if(isset($_GET['link']))
			$hts->child_remove($uri, $_GET['link']);

		go("$uri?nav_edit");
	}

    function handler_edit_nav_parent_add($uri, $action)
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(3, $uri))
			return true;

		$hts = new DataBaseHTS;
		
		if(isset($_POST['link']))
			$hts->parent_add($uri, $_POST['link']);

		go("$uri?nav_edit");
	}

    function handler_edit_nav_parent_delete($uri, $action)
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(3, $uri))
			return true;

		$hts = new DataBaseHTS;
		
		if(isset($_GET['link']))
			$hts->parent_remove($uri, $_GET['link']);

		go("$uri?nav_edit");
	}
?>