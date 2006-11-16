<?
	if(!empty($_GET['dbg']))
		DebugBreak();

    register_action('new-ticket-post', 'plugins_ticket_system_new_ticket_post');

	hts_data_prehandler("", array(
			'body' => 'plugins_ticket_system_body',
			'title' => ec('Система задач'),
			'nav_name' => ec('Система задач'),
		));

	hts_data_prehandler("\d+/", array(
			'body'   => 'plugins_ticket_system_ticket_body',
			'source' => 'default',
			'modify_time' => 'default',
			'create_time' => 'default',
		));

	include_once("include/funcs.php");

	function plugins_ticket_system_body($uri, $m, $plugin_data)
	{
		$items_per_page = 50;
	
		include_once("funcs/datetime.php");
	
		$data = array();
		
		$data['base_uri'] = $plugin_data['base_uri'];
		
//		echo "<xmp>"; print_r($GLOBALS['cms']); echo "</xmp>";
//		echo "=={$GLOBALS['cms']['plugins']['tickets']['db']}==";
		$hts = &new DataBaseHTS(@$GLOBALS['cms']['plugins']['tickets']['db']);
//		echo ":".$hts->dbh->db_name;

		$tickets = array();

        $total = $hts->get_children_array_ex_size($data['base_uri'], array('closed' => 'no', 'range' => -1));

		include_once('funcs/design/page_split.php');
																		
		$page = max(intval($GLOBALS['cms']['page_number']), 1);

		$pages = join(" ", pages_select($uri, $page, ($total-1)/$items_per_page+1));
																						
		$data['pages'] = $pages;
		$start = ($page-1)*$items_per_page;
		$limit = $items_per_page;

//		$GLOBALS['log_level'] = 10;
		foreach($hts->get_children_array_ex($data['base_uri'], array('closed' => 'no', 'range' => -1, 'start' => $start, 'limit' => $limit)) as $ticket)
			$tickets[] = array(
				'uri'    => $ticket,
				'title'  => $hts->get_data($ticket, 'title'),
				'date'   => news_time($hts->get_data($ticket, 'modify_time')),
				'create_date'   => news_time($hts->get_data($ticket, 'create_time')),
				'priority'	=> get_priority($hts->get_data($ticket, 'priority')),
				'color'	=> get_color($hts->get_data($ticket, 'priority')),
				'category'	=> get_category($hts->get_data($ticket, 'category')),
				'closed' => false,
			);

		$data['tickets'] = $tickets;
		$data['plugin'] = $plugin_data;
		
        include_once("funcs/templates/assign.php");
        return template_assign_data("tickets-list.htm", $data);
	}

	function plugins_ticket_system_ticket_body($uri, $m, $plugin_data)
	{
		include_once("funcs/datetime.php");
	
		$data = array();
		
		$data['base_uri'] = $plugin_data['base_uri'];
		
		$hts = &new DataBaseHTS(@$GLOBALS['cms']['plugins']['tickets']['db']);

//		print_r($hts->get_data_array($data['base_uri'], 'child'));

		$posts = array();

		$data['title']  = $hts->get_data($uri, 'title');
		$data['closed'] = $hts->is_flag($uri, 'closed');
		
		$posts[] = array(
			'message' => lcml($hts->get_data($uri, 'source'), array(
				'html' => false,
				'cache_disable' => true,
				'cr_type'=>'save_cr')),
			'author_name' => $hts->get_data($uri, 'author_name'),
			'date' => news_time($hts->get_data($uri, 'create_time')),
		);

//		print_r($uri);

		foreach($hts->get_children_array_ex($uri, array('order'=>'create_time asc', 'range' => -1)) as $post)
			$posts[] = array(
				'message' => lcml($hts->get_data($post, 'source'), array('with_html'=>true, 'cr_type'=>'save_cr')),
				'author_name' => $hts->get_data($post, 'author_name'),
				'date' => news_time($hts->get_data($post, 'create_time')),
			);

		$data['posts'] = $posts;
		$data['plugin'] = $plugin_data;

        include_once("funcs/templates/assign.php");
        return template_assign_data("ticket.htm", $data);
	}

	hts_data_prehandler("new_ticket/", array(
			'body' => 'plugins_ticket_system_new_ticket_body',
			'title' => ec('Создание новой задачи'),
		));

	function plugins_ticket_system_new_ticket_body($uri, $m, $plugin_data)
	{
		$data = array();
		
		$data['base_uri'] = $plugin_data['base_uri'];
		$data['plugin'] = $plugin_data;

        include_once("funcs/templates/assign.php");
        return template_assign_data("new_ticket.htm", $data);
	}


    function plugins_ticket_system_new_ticket_post($uri, $action, $m, $plugin_data)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$hts = &new DataBaseHTS(@$GLOBALS['cms']['plugins']['tickets']['db']);
		$us = &new User;

		if(!$us->data('id'))
			return error_message(ec("Вы не вошли в систему"));

		if(empty($_POST['title']))
			return error_message(ec("Вы не указали заголовок сообщения."));

		if(empty($_POST['source']))
			return error_message(ec("Вы не написали текст сообщения."));

		if(@$_POST['category'] == -1)
			return error_message(ec("Вы не указали блок."));

		if(@$_POST['priority'] == -1)
			return error_message(ec("Вы не указали приоритет."));
		
		$new_ticket = $plugin_data['base_uri'].get_new_global_id(@$GLOBALS['cms']['plugins']['tickets']['db'])."/";

//		print_r($_POST);
//		echo $GLOBALS['cms']['plugin_base_uri'];
//		exit($new_ticket);

		$hts->nav_link($plugin_data['base_uri'], $new_ticket);

		$hts->set_data($new_ticket, 'title',  $_POST['title']);
		$hts->set_data($new_ticket, 'source', $_POST['source']);
		$hts->set_data($new_ticket, 'create_time', time());
		$hts->set_data($new_ticket, 'modify_time', time());
		$hts->set_data($new_ticket, 'author', $us->data('id'));
		$hts->set_data($new_ticket, 'author_name', $us->data('name'));

		if(!empty($_POST['priority']))
			$hts->set_data($new_ticket, 'priority',  $_POST['priority']);

		if(!empty($_POST['category']))
			$hts->set_data($new_ticket, 'category',  $_POST['category']);

		$us->set_data("last_answer", time());
		$us->set_data("last_post_hash", md5($_POST['source']));

		go($new_ticket);
		return true;
	}

    register_action('comment-add', 'plugins_ticket_system_comment_add');

    function plugins_ticket_system_comment_add($uri, $action, $match, $plugin_data)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$hts = &new DataBaseHTS(@$GLOBALS['cms']['plugins']['tickets']['db']);
		$us = &new User;

		if(!$us->data('id') || !$us->data('name'))
			return error_message(ec("Вы не вошли в систему"));

		if(empty($_POST['source']))
			return error_message(ec("Вы не написали текст сообщения."));

		$comment = $plugin_data['base_uri']."comment".get_new_global_id()."/";

//		exit($comment);

		$hts->nav_link($uri, $comment);

		$hts->set_data($uri, 'modify_time', time());

		$hts->set_data($comment, 'title',  @$_POST['title']);
		$hts->set_data($comment, 'source', $_POST['source']);
		$hts->set_data($comment, 'create_time', time());
		$hts->set_data($comment, 'modify_time', time());
		$hts->set_data($comment, 'author', $us->data('id'));
		$hts->set_data($comment, 'author_name', $us->data('name'));

		$us->set_data("last_answer", time());
		$us->set_data("last_post_hash", md5($_POST['source']));

		include_once('actions/recompile.php');
		recompile($uri);
		go(@$plugin_data['base_uri']."?");
		return true;
	}

	register_action('ticket-close', 'plugins_ticket_system_ticket_close');
    function plugins_ticket_system_ticket_close($uri, $action, $match, $plugin_data)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$hts = &new DataBaseHTS(@$GLOBALS['cms']['plugins']['tickets']['db']);
		$us = &new User;

		if(!$us->data('id') || !$us->data('name'))
			return error_message(ec("Вы не вошли в систему"));

		$hts->set_flag($uri, 'closed');

		recompile($uri);
		go(@$plugin_data['base_uri']);
		return true;
	}

	register_action('ticket-open', 'plugins_ticket_system_ticket_open');
    function plugins_ticket_system_ticket_open($uri, $action)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$hts = &new DataBaseHTS(@$GLOBALS['cms']['plugins']['tickets']['db']);
		$us = &new User;

		if(!$us->data('id') || !$us->data('name'))
			return error_message(ec("Вы не вошли в систему"));

		$hts->drop_flag($uri, 'closed');

		recompile($uri);
		go($uri);
		return true;
	}
