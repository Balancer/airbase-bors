<?php

$_SERVER['DOCUMENT_ROOT'] = '/var/www/balancer.ru/htdocs';

require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
require_once('funcs/DataBase.php');
require_once('funcs/js.php');

update_users_online();

function update_users_online()
{
	$db = &new DataBase('punbb');

	$timeout = time() - 900;
	$idle    = time() - 300;
	
	// Fetch all online list entries that are older than "o_timeout_online"
	foreach($db->get_array("SELECT * FROM online WHERE logged < $timeout") as $cur_user)
	{
		// If the entry is a guest, delete it
		if ($cur_user['user_id'] == '1')
			$db->query('DELETE FROM online WHERE ident=\''.addslashes($cur_user['ident']).'\'');
		else
		{
			// If the entry is older than "o_timeout_visit", update last_visit for the user in question, then delete him/her from the online list
			if ($cur_user['logged'] < $idle)
			{
				$db->query('UPDATE users SET last_visit='.$cur_user['logged'].' WHERE id='.$cur_user['user_id']);
				$db->query('DELETE FROM online WHERE user_id='.$cur_user['user_id']);
			}
			else if ($cur_user['idle'] == '0')
				$db->query('UPDATE online SET idle=1 WHERE user_id='.$cur_user['user_id']);
		}
	}

	$num_guests = 0;
	$users = array();

	foreach($db->get_array('SELECT user_id, ident FROM online WHERE idle=0 ORDER BY ident') as $pun_user_online)
	{
		if ($pun_user_online['user_id'] > 1)
			$users[] = "\n\t\t\t\t"."<dd><a href=\"{$pun_config['root_uri']}/profile.php?id={$pun_user_online['user_id']}\">".htmlspecialchars($pun_user_online['ident']).'</a>';
		else
			$num_guests++;
	}

	$num_users = count($users);

	$online = '<dd>'. ec('Сейчас зарегистрированных посетителей') .': <strong>'.$num_users.'</strong></dd>'."\n\t\t\t\t".'<dd>'.ec('Сейчас гостей').': <strong>'.$num_guests.'</strong></dd>'."\n\t\t\t".'</dl>'."\n";

	if ($num_users > 0)
		$online .= '<dl id="onlinelist" class= "clearb"><dt><strong>'.ec('Активны').':&nbsp;</strong></dt>'."\t\t\t\t".implode(',</dd> ', $users).'</dd>'."\n\t\t\t".'</dl>'."\n";
	else
		$online .= '<div class="clearer"></div>'."\n";

	@file_put_contents($file = '/var/www/balancer.ru/htdocs/js/stat-users.js', str2js($online));
	@chmod($file, 0666);

	$os = array();
	foreach($db->get_array("SELECT os, count(*) as cnt FROM online GROUP BY os ORDER BY cnt DESC, os") as $row)
	{
		if(!$row['os'])
			$row['os'] = "Неизвестно";
			
		$perc = sprintf("%02.1f", 100*$row['cnt']/($num_users+$num_guests)+0.5);
		
		$os[] = "<span style=\"color: blue;\">{$row['os']}</span> ({$row['cnt']})";
	}

	@file_put_contents($file = '/var/www/balancer.ru/htdocs/js/stat-os.js', str2js("<dl id=\"onlinelist\" class= \"clearb\"><dt><b>Операционные системы:</b> </dt><dd>".join(", ", $os)."</dd></dl>"));
	@chmod($file, 0666);
	
	$browsers = array();
	foreach($db->get_array("SELECT browser, count(*) as cnt FROM online GROUP BY browser ORDER BY cnt DESC, browser") as $row)
	{
		if(!$row['browser'])
			$row['browser'] = "Неизвестно";
			
		$perc = sprintf("%02.1f", 100*$row['cnt']/($num_users+$num_guests)+0.5);
		
		$browsers[] = "<span style=\"color: blue;\">{$row['browser']}</span> ({$row['cnt']})";
	}

	$browsers = join(", ", $browsers);
	
	@file_put_contents($file = '/var/www/balancer.ru/htdocs/js/stat-browsers.js', str2js("<dl id=\"onlinelist\" class= \"clearb\"><dt><b>Браузеры: </dt><dd>$browsers</dd></dl>"));
	@chmod($file, 0666);
}
