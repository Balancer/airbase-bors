<?
	if(!empty($_GET['dbg']))
		DebugBreak();

	hts_data_prehandler("!^(http://[^/]+/)[a-z]+/?$!", array(
			'body' => 'plugins_ticket_system_body',
			'title' => ec('Система тикетов'),
		));

	function get_category($cat)
	{
		$cats = array(
			"news"=>'Новости',
			"conferences"=>'Конференция',
			"list"=>'Рассылка',
			"job"=>'Персонал',
			"torg"=>'Торг',
			""=>'Общий',
		);
		
		if(isset($cats[$cat]))
			return ec($cats[$cat]);
		
		return $cat;
	}

	function get_priority($priority)
	{
		$priors = array(
			"critical"=>'Критическая ошибка',
			"error"=>'Ошибка',
			"fix"=>'Исправление',
			""=>'Дополнение',
			"task"=>'Второстепенная задача',
		);
		
		if(isset($priors[$priority]))
			return ec($priors[$priority]);
		
		return $priority;
	}

	function get_color($priority)
	{
		$colors = array(
			"critical"=>'#ffc0c0',
		);
		
		if(isset($colors[$priority]))
			return " style=\"background-color: $colors[$priority];\"";
		
		return '';
	}

	function plugins_ticket_system_body($uri, $m)
	{
		include_once("funcs/datetime.php");
	
		$data = array();
		
		$data['base_uri'] = $GLOBALS['cms']['plugin_base_uri'];
		
		$hts = new DataBaseHTS;

//		print_r($hts->get_data_array($data['base_uri'], 'child'));

		$tickets = array();

//		$GLOBALS['log_level'] = 10;

		foreach($hts->get_children_array_ex($data['base_uri'], array('closed' => 'no', 'range' => -1)) as $ticket)
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

//		$GLOBALS['log_level'] = 2;
		
		foreach($hts->get_children_array_ex($data['base_uri'], array('closed' => 'yes', 'range' => -1)) as $ticket)
			$tickets[] = array(
				'uri'    => $ticket,
				'title'  => $hts->get_data($ticket, 'title'),
				'date'   => news_time($hts->get_data($ticket, 'modify_time')),
				'create_date'   => news_time($hts->get_data($ticket, 'create_time')),
				'priority'	=> get_priority($hts->get_data($ticket, 'priority')),
				'color'	=> get_color($hts->get_data($ticket, 'priority')),
				'category'	=> get_category($hts->get_data($ticket, 'category')),
				'closed' => true,
			);

		$data['tickets'] = $tickets;
		
        include_once("funcs/templates/assign.php");
        return template_assign_data("tickets-list.htm", $data);
	}

	hts_data_prehandler("!^(.+/)\d+/?$!", array(
			'body'   => 'plugins_ticket_system_ticket_body',
			'source' => 'default',
			'modify_time' => 'default',
			'create_time' => 'default',
		));

	function plugins_ticket_system_ticket_body($uri, $m)
	{
		include_once("funcs/datetime.php");
	
		$data = array();
		
		$data['base_uri'] = $GLOBALS['cms']['plugin_base_uri'];
		
		$hts = new DataBaseHTS;

//		print_r($hts->get_data_array($data['base_uri'], 'child'));

		$posts = array();

		$data['title']  = $hts->get_data($uri, 'title');
		$data['closed'] = $hts->is_flag($uri, 'closed');
		
		$posts[] = array(
			'message' => lcml($hts->get_data($uri, 'source'), array('with_html'=>true, 'cr_type'=>'save_cr')),
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

        include_once("funcs/templates/assign.php");
        return template_assign_data("ticket.htm", $data);
	}

	hts_data_prehandler("!^(.+/)new_ticket/?$!", array(
			'body' => 'plugins_ticket_system_new_ticket_body',
			'title' => ec('Создание нового тикета'),
		));

	function plugins_ticket_system_new_ticket_body($uri, $m)
	{
		$data = array();
		
		$data['base_uri'] = $GLOBALS['cms']['plugin_base_uri'];

        include_once("funcs/templates/assign.php");
        return template_assign_data("new_ticket.htm", $data);
	}

    register_action_handler('new-ticket-post', 'plugins_ticket_system_new_ticket_post');

    function plugins_ticket_system_new_ticket_post($uri, $action)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$ht = new DataBaseHTS;
		$us = new User;

		if(!$us->data('id'))
			return error_message(ec("Вы не вошли в систему"));

		if(empty($_POST['title']))
			return error_message(ec("Вы не указали заголовок сообщения."));

		if(empty($_POST['source']))
			return error_message(ec("Вы не написали текст сообщения."));

		$new_ticket = $GLOBALS['cms']['plugin_base_uri'].get_new_global_id()."/";

//		print_r($_POST);
//		exit($new_ticket);

		$ht->nav_link($GLOBALS['cms']['plugin_base_uri'], $new_ticket);

		$ht->set_data($new_ticket, 'title',  $_POST['title']);
		$ht->set_data($new_ticket, 'source', $_POST['source']);
		$ht->set_data($new_ticket, 'create_time', time());
		$ht->set_data($new_ticket, 'modify_time', time());
		$ht->set_data($new_ticket, 'author', $us->data('id'));
		$ht->set_data($new_ticket, 'author_name', $us->data('name'));

		if(!empty($_POST['priority']))
			$ht->set_data($new_ticket, 'priority',  $_POST['priority']);

		if(!empty($_POST['category']))
			$ht->set_data($new_ticket, 'category',  $_POST['category']);

		$us->set_data("last_answer", time());
		$us->set_data("last_post_hash", md5($_POST['source']));

		go($new_ticket);
		return true;
	}

    register_action_handler('comment-add', 'plugins_ticket_system_comment_add');

    function plugins_ticket_system_comment_add($uri, $action)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$ht = new DataBaseHTS;
		$us = new User;

		if(!$us->data('id') || !$us->data('name'))
			return error_message(ec("Вы не вошли в систему"));

		if(empty($_POST['source']))
			return error_message(ec("Вы не написали текст сообщения."));

//		echo $GLOBALS['cms']['plugin_base_path'];
//		exit();

		$comment = $GLOBALS['cms']['plugin_base_uri']."comment".get_new_global_id()."/";

//		exit($comment);

		$ht->nav_link($uri, $comment);

		$ht->set_data($comment, 'title',  @$_POST['title']);
		$ht->set_data($comment, 'source', $_POST['source']);
		$ht->set_data($comment, 'create_time', time());
		$ht->set_data($comment, 'modify_time', time());
		$ht->set_data($comment, 'author', $us->data('id'));
		$ht->set_data($comment, 'author_name', $us->data('name'));

		$us->set_data("last_answer", time());
		$us->set_data("last_post_hash", md5($_POST['text']));

		include_once('actions/recompile.php');
		recompile($comment);
		go($uri);
		return true;
	}

	register_action_handler('ticket-close', 'plugins_ticket_system_ticket_close');
    function plugins_ticket_system_ticket_close($uri, $action)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$hts = new DataBaseHTS;
		$us = new User;

		if(!$us->data('id') || !$us->data('name'))
			return error_message(ec("Вы не вошли в систему"));

		$hts->set_flag($uri, 'closed');

		recompile($uri);
		go($GLOBALS['cms']['plugin_base_uri']);
		return true;
	}

	register_action_handler('ticket-open', 'plugins_ticket_system_ticket_open');
    function plugins_ticket_system_ticket_open($uri, $action)
	{
		include_once('funcs/mail.php');
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$hts = new DataBaseHTS;
		$us = new User;

		if(!$us->data('id') || !$us->data('name'))
			return error_message(ec("Вы не вошли в систему"));

		$hts->drop_flag($uri, 'closed');

		recompile($uri);
		go($uri);
		return true;
	}
?>