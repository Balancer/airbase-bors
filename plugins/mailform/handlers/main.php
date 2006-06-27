<?
	hts_data_prehandler("!^({$GLOBALS['cms']['plugin_parent_uri']}).+?$!", array(
			'body' => 'plugins_mailform_main_body',
			'title' => ec('Заказ'),
		));

	function plugins_mailform_main_body($uri, $m)
	{
        include_once("funcs/templates/assign.php");
        return template_assign_data("form.html");
	}

    register_action_handler('send-form', 'plugins_mailform_send_form');

    function plugins_mailform_send_form($uri, $action)
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
