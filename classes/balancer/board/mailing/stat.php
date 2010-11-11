<?php

class balancer_board_mailing_stat extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'BORS'; }
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
<link rel="stylesheet" type="text/css" href="http://balancer.ru/_bors/css/balancer-378-min.css" />
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

				$last_visit = max($last_visit, $user_stat->last_mailing()) - 7*86400;

				$mail .= $target->fetch_updated_from($last_visit, $user_stat->mail_format());
			}
		}

		switch($user_stat->mail_format())
		{
			case 'text':
				file_put_contents('mail.txt', $mail);
				break;
			case 'html':
				file_put_contents('mail.html', $mail);
				break;
			case 'pdf':
				$tmp = 'mail.html'; //tmpfile();
				file_put_contents($tmp, $mail);
				system("/usr/local/bin/wkhtmltopdf-amd64 $tmp $tmp.pdf");
				unlink($tmp);
				break;
			default:
				debug_hidden_log('mailing-errors', "Unknown mail format {$user_stat->mail_format()} at id {$user_stat->mail_format()->id()}");
				break;
		}
	}
}
