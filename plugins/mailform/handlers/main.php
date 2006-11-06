<?
	hts_data_prehandler("", array(
			'body' => 'plugins_mailform_main_body',
			'title' => ec('Заказ'),
			'template' => 'popup',
		));

	function plugins_mailform_main_body($uri, $m)
	{
        include_once("funcs/templates/assign.php");
        return template_assign_data("form.html");
	}

    register_action('send-form', 'plugins_mailform_send_form');

    function plugins_mailform_send_form($uri, $action)
	{
		$GLOBALS['cms']['template_override'] = '/cms/templates/popup/';
	
		include_once('funcs/mail.php');
		require_once('funcs/modules/messages.php');

		$fields = array(
			'fio' => ec('ФИО'),
			'company' => ec('Организация'),
			'email' => ec('E-Mail'),
			'phone' => ec('Телефон'),
			'message' => ec('Сообщение'),
		);
		
		$message = "";
			
		foreach($fields as $key => $name)
		{
			if(empty($_POST[$key]))
				return error_message(ec("Вы не заполнили поле '$name'."));
			$message .= "$name: {$_POST[$key]}\n\n";
		}

		send_mail($_POST['email'], "commerce@1001kran.ru", ec("Заказ"), $message);
		return message(ec("<b>Заказ отправлен</b>"));
	}
