<?php

class balancer_board_mailing_stat extends bors_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_BORS'; }
	function table_name() { return 'bors_mailing_users_stat'; }

	function table_fields()
	{
		return array(
			'id',
			'user_class_name',
			'user_id',
			'mailing_period',
			'last_mailing',
			'mail_format',
		);
	}

	static function do_mailing()
	{
		foreach(bors_find_all('balancer_board_mailing_stat', array(
				'is_active' => true,
				'last_mailing+mailing_period <= ' => time(),
			)) as $user_stat)
		{
			$user = bors_load($user_stat->user_class_name(), $user_stat->user_id());
			if(!$user)
				continue;

			if($user_stat->mail_format() != 'text')
				$mail = '
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://www.balancer.ru/_bors/css/balancer-378-min.css" />
';
			else
				$mail = '';

			$last_mailing = time();
			// Ищем подходящие записи в bors_mailing
			// Последних N штук, не более
			foreach(bors_find_all('balancer_board_mailing_record', array(
				'target_user_id' => $user_stat->user_id(),
				'create_time>=' => $user_stat->last_mailing(),
				'order' => '-create_time',
				'limit' => 10,
			)) as $mail_x)
			{
				$target = $mail_x->target();
				if(method_exists($target, 'last_visit_time_for_user'))
					$last_visit = $target->last_visit_time_for_user($user);
				else
					$last_visit = 0;

				$last_visit = max($last_visit, $user_stat->last_mailing());

				$mail .= $target->fetch_updated_from($last_visit, $user_stat->mail_format());
			}
		}

		$to = bors_ext_mail::make_recipient($user);
		$subject = "[forums.balancer.ru] Обновлённые темы форумов";

		echo "Send to $to\n";

		require_once('engines/mail.php');
		switch($user_stat->mail_format())
		{
			case 'text':
				$mail = "Сообщение в текстовом формате. Чтобы изменить формат или изменить условия подписки посетите адрес ...\n\n"
					.$mail;
				send_mail($to, $subject, $mail, NULL, 'balabot@balancer.ru');
				break;
			case 'html':
				$mail = "<p>Сообщение в формате HTML. Чтобы изменить формат или изменить условия подписки посетите адрес ...</p>\n\n"
					.$mail;
				send_mail($to, $subject, NULL, $mail, 'balabot@balancer.ru');
				break;
			case 'pdf':
				$tmp = tempnam(sys_get_temp_dir(), 'mailing').'.html';
				$pdf = tempnam(sys_get_temp_dir(), 'mailing').'.pdf';
				file_put_contents($tmp, $mail);
//				echo "$tmp -> $pdf\n";
				system("/usr/local/bin/wkhtmltopdf-amd64 $tmp $pdf");
				$mail = "Сообщение в формате PDF. Чтобы изменить формат или изменить условия подписки посетите адрес ...:\n\n";
//				echo $pdf;
				send_mail($to, $subject, $mail, NULL, 'balabot@balancer.ru', NULL, array(array(
					'file' => $pdf,
					'name' => 'balancer-'.date('Y-m-d_H-i-s'),
					'type' => 'application/pdf',
				)));
				unlink($tmp);
				unlink($pdf);
				break;
			default:
				bors_debug::syslog('mailing-errors', "Unknown mail format {$user_stat->mail_format()} at id {$user_stat->mail_format()->id()}");
				break;
		}
	}
}
