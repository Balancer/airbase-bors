<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
include_once(BORS_CORE.'/init.php');

if(empty($_SERVER['argv'][1]))
	bors_exit("Укажи файл");

echo "Load file {$_SERVER['argv'][1]}\n";
$text = file_get_contents($_SERVER['argv'][1]);

$from = bors_load('balancer_board_user', 10000);

$user_id = 10000;
$user = bors_load('balancer_board_user', $user_id);

foreach(bors_find_all('balancer_board_user', array('group_id' => 3)) as $user)
{
	echo "Send to {$user->title()} <{$user->email()}> ...";
	bors_ext_mail::send($user, $text, $from);
	$user->set_group_id(4, true);
	echo "\tok\n";
}
