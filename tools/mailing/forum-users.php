<?php

require '../config.php';

if(empty($_SERVER['argv'][1]))
	bors_exit("Укажи файл");

echo "Load file {$_SERVER['argv'][1]}\n";
$text = file_get_contents($_SERVER['argv'][1]);

$from = bors_load('balancer_board_user', 84069);

$user_id = 1615;
$user = bors_load('balancer_board_user', $user_id);

foreach(bors_find_all('balancer_board_user', [
			'has_invalid_email' => false,
			'is_dead' => false,
			'is_deleted' => false,
			'last_visit_time<' => time() - 86400*14,
]) as $user)
{
	$ban = $user->is_admin_banned();
	if($ban && !preg_match('/просьбе/', $ban->message()))
		continue;

	echo "Send to {$user->title()} <{$user->email()}> ...";
	bors_ext_mail::send($user, $text, $from);
//	$user->set_group_id(4, true);
	echo "\tok\n";
}
