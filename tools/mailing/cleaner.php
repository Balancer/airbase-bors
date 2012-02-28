<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
include_once(BORS_CORE.'/init.php');

function not_found($email, $message, $file, $delete=true)
{
	echo "Not found user {$email}\tSet invalid for ";

	foreach(bors_find_all('balancer_board_user', array('email' => $email)) as $u)
	{
		echo "{$u->title()}";
		$u->set_has_invalid_email(true, true);
		$u->set_invalid_mail_message($message, true);
	}

	echo " [".str_replace("\n", " ", $message)."]\n";
	if($delete)
		unlink($file);
}

foreach(glob('/home/balancer/.maildir/new/*') as $f)
{
	$content = file_get_contents($f);

	if(preg_match("/^(<(\S+@\S+)> \(expanded from <(\S+@\S+)>\): host\s+\S+\s+said: .+(\n.+)+)\n\n/m", $content, $m))
	{
		not_found($m[3], $m[1], $f);
		continue;
	}

	if(preg_match("/\n\s+The mail system\n\n(<(\S+@\S+)>:.+(\n.+)*)\n\n/", $content, $m))
	{
		not_found($m[2], $m[1], $f);
		continue;
	}

	if(preg_match("/\n\n\s+((\S+@\S+)(\n[^\n]+)+)\n\n.+\nTo: (\S+@\S+)\n.+?$/s", $content, $m))
	{
		not_found($m[4], $m[1], $f);
		continue;
	}

//The following message to <sale@unsi.net> was undeliverable.
//The reason for the problem:
//5.1.1 - Bad destination email address 'reject'

	if(preg_match("/\n(The following message to <(\S+@\S+)> was undeliverable.\nThe reason for the problem:\n.+)\n\n/", $content, $m))
	{
		not_found($m[2], $m[1], $f);
		continue;
	}

	if(preg_match("/\n\s+The mail system\n\n\s+(--- Delivery report unavailable ---)\n.+\nTo: (\S+@\S+)\n.+?$/s", $content, $m))
	{
		not_found($m[2], $m[1], $f);
		continue;
	}

	if(preg_match("/The following error was given:\n\n\s+(.+ (\S+@\S+) is unavailable: .+)\n\n/", $content, $m))
	{
		not_found($m[2], $m[1], $f);
		continue;
	}
}
