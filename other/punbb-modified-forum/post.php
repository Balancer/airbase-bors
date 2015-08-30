<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/

$GLOBALS['cms']['cache_disabled'] = true;
$GLOBALS['cms']['cant_lock'] = true;

# $forum_temporary_redirect = 'http://home.balancer.ru/mybb/search.php?action=getdaily';

define('PUN_ROOT', __DIR__.'/');
require PUN_ROOT.'include/common.php';

require PUN_ROOT.'include/attach/attach_incl.php'; //Attachment Mod row, loads variables, functions and lang file
require 'inc/design/make_quote.php';

/*
if(time()-filemtime("/var/log/started.log") < 3600)
{
	header("Status: 302 Moved Temporarily");
	header("Location: http://home.balancer.ru/mybb/thread-2672.html");
	bors_exit();
}
*/

if(bors_stop_bots('__nobots_testing', 'post'))
	return;

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);

$tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if ($tid < 1 && $fid < 1 || $tid > 0 && $fid > 0)
	message($lang_common['Bad request']);

if($is_banned)
	message("У Вас нет доступа к этой возможности до ".strftime("%Y-%m-%d %H:%M", WARNING_DAYS*86400+$ban_expire)
		.'<br/><br/>'.bbf_bans::message_ls()
	);


$topic = bors_load('balancer_board_topic', $tid);
$forum_id = $fid ? $fid : $topic->forum_id();
$forum = bors_load('balancer_board_forum', $forum_id);

$me = bors()->user();

if(!$me)
	message("Вы не авторизованы на форуме.
	Попробуйте <a href=\"{$forum->category()->category_base_full()}login.php\">авторизоваться</a> снова.<br/><br/>
	<a href=\"http://www.wrk.ru/forums/register.php\" style=\"display: block; width: 24ex; font-size: 10pt; padding: 2px 4px; text-align: center; box-shadow: 2px 2px 4px rgba(0,0,0,0.5); color: white; background: rgb(28, 184, 65)\">Зарегистрироваться</a>
");

if($fid && !$tid && ($me->num_posts() < 3 || $me->create_time() > time() - 86400))
{
	message('Извините, но с целью борьбы со спамерами только что зарегистрированным'
		.' пользователям запрещено создавать новые темы. Поучаствуйте сперва в обсуждениях'
		.' уже имеющихся тем (<a href="http://www.balancer.ru/tools/search/">Поиск в Вашем распоряжении</a>)'
		.' или подождите сутки с момента регистрации. Можете также начать новое'
		.' обсуждение в продолжение уже имеющейся темы с просьбой к координаторам о выносе сообщения с ответами в новую тему.');

	bors_debug::syslog('new-user-try-post', "Новичок пытается создать сообщение: [owner={$me}, num_posts={$me->num_posts()}, registered={$me->ctime()}]");
}

$messages_limit = $me->messages_daily_limit();
if($messages_limit >= 0)
{
	$today_posted = $me->today_posted_in_forum($forum_id);
	$messages_rest = $messages_limit - $today_posted;

	if($messages_rest <= 0)
	{
		require_once('inc/datetime.php');
		message("Вы не можете больше отправить ни одного сообщения в этот форум до <b>".full_time($me->next_can_post($messages_limit, $forum_id))."</b>. 
		Подробности в теме <a href=\"http://www.balancer.ru/support/2009/07/t67998--ogranichenie-sutochnogo-chisla-soobschenij-dlya-polzovatelej.1757.html\">Ограничение суточного числа сообщений</a>"
		.'<br/><br/>'.bbf_bans::message_ls()
		);
	}
}

if($warnings_total = $me->warnings())
	if(($warnings_in = $me->warnings_in($forum_id)) >= 5)
		message("Вы не можете больше отправить ни одного сообщения в этот форум, пока количество активных штрафных баллов равно пяти или более. Сейчас оно равно $warnings_in. 
		Подробности в теме <a href=\"http://www.balancer.ru/support/2009/07/t68005--poforumnye-ogranicheniya-5-shtrafov.4435.html\">Пофорумные ограничения</a>."
		.'<br/><br/>'.bbf_bans::message_ls()
		);

$forum = bors_load('balancer_board_forum', $forum_id);

// Fetch some info about the topic and/or the forum
if ($tid)
	$result = $db->query('SELECT f.id, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.subject, t.closed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$tid) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT f.id, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$cur_posting = $db->fetch_assoc($result);

// Is someone trying to post into a redirect forum?
if ($cur_posting['redirect_url'] != '')
	message($lang_common['Bad request']);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_posting['moderators'] != '') ? unserialize($cur_posting['moderators']) : array();
$is_admmod = ($pun_user['g_id'] == PUN_ADMIN || ($pun_user['g_id'] == PUN_MOD && array_key_exists($pun_user['username'], $mods_array))) ? true : false;

//var_dump($cur_posting); var_dump($pun_user);
// Do we have permission to post?
if((
		($tid && (($cur_posting['post_replies'] == '' && $pun_user['g_post_replies'] == '0') || $cur_posting['post_replies'] == '0'))
		|| ($fid && (($cur_posting['post_topics'] == '' && $pun_user['g_post_topics'] == '0') || $cur_posting['post_topics'] == '0'))
		|| (isset($cur_posting['closed']) && $cur_posting['closed'] == '1')
	) && !$is_admmod)
	message($lang_common['No permission']);

// Load the post.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/post.php';

// Start with a clean slate
$errors = array();

include_once("inc/system.php");

$qid = 0;
if(isset($_GET['qid']))
{
	$qid = intval($_GET['qid']);
	if ($qid < 1)
		message($lang_common['Bad request']);
}

if(isset($_POST['qid']))
{
	$qid = intval($_POST['qid']);
	if ($qid < 1)
		message($lang_common['Bad request']);
}

if($qid)
{
	$quoted_post = bors_load('balancer_board_post', $qid);
	$topic = $quoted_post->topic();
	$tid = $topic->id();
}

$GLOBALS['cms']['cache_disabled'] = true;

$true_text = 100;

// Did someone just hit "Submit" or "Preview"?
if (isset($_POST['form_sent']))
{
	config_set('lcml_cache_disable', true);
	config_set('lcml_cache_disable_full', true);

//	if (($pun_user['is_guest'] && $_POST['form_user'] != 'Guest') || (!$pun_user['is_guest'] && $_POST['form_user'] != $pun_user['username']))
//		message($lang_common['Bad request']);

	// Flood protection
	if (!$pun_user['is_guest'] && !isset($_POST['preview']) && $pun_user['last_post'] != '' && (time() - $pun_user['last_post']) < $pun_user['g_post_flood'])
		$errors[] = $lang_post['Flood start'].' '.$pun_user['g_post_flood'].' '.$lang_post['flood end'];

	if(!empty($_POST['as_new_post']) && $tid)
	{
		$fid = $topic->forum_id();
		$tid = 0;
	}

	// If it's a new topic
	if ($fid)
	{
		$subject 	= pun_trim($_POST['req_subject']);
		$description= pun_trim($_POST['nreq_description']);

		if ($subject == '')
			$errors[] = $lang_post['No subject'];
		else if (pun_strlen($subject) > 255)
			$errors[] = $lang_post['Too long subject'];
		else if ($pun_config['p_subject_all_caps'] == '0' && strtoupper($subject) == $subject && $pun_user['g_id'] > PUN_MOD)
			$subject = ucwords(strtolower($subject));

		require_once('inc/strings.php');
	}

	// If the user is logged in we get the username and e-mail from $pun_user
	if (!$pun_user['is_guest'])
	{
		$username = $me->title();
		$email = $pun_user['email'];
	}
	// Otherwise it should be in $_POST
	else
	{
		$username = trim($_POST['req_username']);
		$email = strtolower(trim(($pun_config['p_force_guest_email'] == '1') ? $_POST['req_email'] : $_POST['email']));

		// Load the register.php/profile.php language files
		require PUN_ROOT.'lang/'.$pun_user['language'].'/prof_reg.php';
		require PUN_ROOT.'lang/'.$pun_user['language'].'/register.php';

		// It's a guest, so we have to validate the username
		if (strlen($username) < 2)
			$errors[] = $lang_prof_reg['Username too short'];
		else if (!strcasecmp($username, 'Guest') || !strcasecmp($username, $lang_common['Guest']))
			$errors[] = $lang_prof_reg['Username guest'];
		else if (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $username))
			$errors[] = $lang_prof_reg['Username IP'];

		if ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
			$errors[] = $lang_prof_reg['Username reserved chars'];
		if (preg_match('#\[b\]|\[/b\]|\[u\]|\[/u\]|\[i\]|\[/i\]|\[color|\[/color\]|\[quote\]|\[quote=|\[/quote\]|\[code\]|\[/code\]|\[img\]|\[/img\]|\[url|\[/url\]|\[email|\[/email\]#i', $username))
			$errors[] = $lang_prof_reg['Username BBCode'];

		// Check username for any censored words
		$temp = censor_words($username);
		if ($temp != $username)
			$errors[] = $lang_register['Username censor'];

		// Check that the username (or a too similar username) is not already registered
		$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE username=\''.$db->escape($username).'\' OR username=\''.$db->escape(preg_replace('/[^\w]/', '', $username)).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
		{
			$busy = $db->result($result);
			$errors[] = $lang_register['Username dupe 1'].' '.pun_htmlspecialchars($busy).'. '.$lang_register['Username dupe 2'];
		}

		if ($pun_config['p_force_guest_email'] == '1' || $email != '')
		{
			require PUN_ROOT.'include/email.php';
			if (!is_valid_email($email))
				$errors[] = $lang_common['Invalid e-mail'];
		}
	}

	// Clean up message from POST
	$message = pun_linebreaks(pun_trim($_POST['req_message']));
	$message = bors_markup_prepare::parse($message);

	if ($message == '')
		$errors[] = $lang_post['No message'];
	else if (strlen($message) > 65535)
		$errors[] = $lang_post['Too long message'];
	else if ($pun_config['p_message_all_caps'] == '0' && strtoupper($message) == $message && $pun_user['g_id'] > PUN_MOD)
		$message = ucwords(strtolower($message));

	// Validate BBCode syntax
	if ($pun_config['p_message_bbcode'] == '1' && strpos($message, '[') !== false && strpos($message, ']') !== false)
	{
		require PUN_ROOT.'include/parser.php';
		$message = preparse_bbcode($message, $errors);
	}

	require PUN_ROOT.'include/search_idx.php';

	$hide_smilies = isset($_POST['hide_smilies']) ? 1 : 0;
	$subscribe = isset($_POST['subscribe']) ? 1 : 0;

	$now = time();

	if(!isset($_POST['preview']))
	{
		if(bors_strlen($message) > 400 && preg_match('/^\S+>/', $message))
		{
			$q = 0;
			$a = 0;
			foreach(explode("\n", $message) as $s)
			{
				$s = trim($s);
				if($s == '')
					continue;
				if(preg_match('/^\S+>/', $s))
					$q += bors_strlen($s);
				else
					$a += bors_strlen($s);
			}

			$true_text = $a/($a+$q)*100+0.0001;
		}
	}

	if(!empty($_POST['overquote_confirmed']))
		$true_text = 100;

	$was_notified = false;

	// Did everything go according to plan?
	if(empty($errors) && !isset($_POST['preview']) && $true_text > 40)
	{
		$md = md5($message);
		if($me->last_message_md() == $md)
			message("Вы уже отправили это сообщение");

		$me->set_last_message_md($md, true);
		$answer_to_post = bors_load('balancer_board_post', @$qid);

		// If it's a reply
		if ($tid)
		{
			if (!$pun_user['is_guest'])
			{
				$post = bors_new('balancer_board_post', array(
					'author_name' => $username,
					'owner_id' => $pun_user['id'], 
					'poster_ip' => get_remote_address(), 
					'poster_ua' => @$_SERVER['HTTP_USER_AGENT'],
					'hide_smilies' => $hide_smilies, 
					'create_time' => $now, 
					'topic_id' => $tid,
					'answer_to_id' => $qid,
					'answer_to_user_id' => $answer_to_post ? $answer_to_post->owner_id() : 0,
					'post_source' => $message,
				));

				// To subscribe or not to subscribe, that ...
				if ($pun_config['o_subscriptions'] == '1' && $subscribe)
				{
					$result = $db->query('SELECT 1 FROM '.$db->prefix.'subscriptions WHERE user_id='.$pun_user['id'].' AND topic_id='.$tid) or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());
					if (!$db->num_rows($result))
						$db->query('INSERT INTO '.$db->prefix.'subscriptions (user_id, topic_id) VALUES('.$pun_user['id'].' ,'.$tid.')') or error('Unable to add subscription', __FILE__, __LINE__, $db->error());
				}
			}
			else
			{
				// It's a guest. Insert the new post
				$post = bors_new('balancer_board_post', array(
					'author_name' => $username, 
					'poster_ip' => get_remote_address(), 
					'poster_ua' => @$_SERVER['HTTP_USER_AGENT'],
					'poster_email' => ($pun_config['p_force_guest_email'] == '1' || $email != '') ? $email : '', 
					'hide_smilies' => $hide_smilies, 
					'create_time' => $now, 
					'topic_id' => $tid,
					'answer_to_id' => $qid,
					'answer_to_user_id' => $answer_to_post ? $answer_to_post->owner_id() : 0,
					'post_source' => $message,
				));

			}

			$user = $post->owner();

			if($qid)
			{
				// Пометим сообщение, на котрое отвечали, что на него есть ответы.
				if($answer_to_post = bors_load('balancer_board_post', $qid))
				{
					if($answer_to_post->have_answers())
					{
						if($answer_to_post->have_answers() > 0)
							$answer_to_post->set_have_answers(-1, true);
					}
					else
						$answer_to_post->set_have_answers($post->id(), true);

					if($answer_to_user = $answer_to_post->owner())
					{
						if($answer_to_user->id() != $user->id())
						{
							$text = "{$user->title()} отвечает на Ваше сообщение:\n"
								.trim(html_entity_decode(make_quote($user->title(), htmlspecialchars(strip_tags($post->source())), false), ENT_COMPAT, 'UTF-8'))
								."\n\n// #{$post->id()} {$post->url_for_igo()} в теме «{$post->topic()->title()}»";

							if($answer_to_user->xmpp_notify_enabled())
							{
								$answer_to_user->notify_text($text);
								$was_notified = $answer_to_user;
							}

							if($joke_user = $answer_to_post->joke_owner())
							{
								$text = "Здравствуйте, {$joke_user->title()}.\n\n"
									."{$user->title()} отвечает на Ваше сообщение:\n"
									."---------------------------------------------------------------\n"
									.$post->source()."\n"
									."// {$post->url_for_igo()} в теме «{$post->topic()->title()}»\n"
									."---------------------------------------------------------------\n"
									."\nВаше исходное сообщение:\n"
									."---------------------------------------------------------------\n"
									.$answer_to_post->source()."\n"
									."// {$answer_to_post->url_for_igo()}\n";

								$joke_user->email_text($text);
							}
						}
					}
				}
			}

			// Count number of replies in the topic

			$num_replies = bors_count('balancer_board_posts_pure', array(
				'topic_id' => $tid,
				'is_deleted' => 0,
			)) - 1;

			$topic->set_num_replies($num_replies);
			$topic->set_last_post_id($post->id());
			$topic->set_last_poster_name($username);
			$topic->store();
			$topic->recalculate();

			update_forum($cur_posting['id']);

			$new_pid = $post->id();

			// Should we send out notifications?
			if ($pun_config['o_subscriptions'] == '1')
			{
				// Get the post time for the previous post in this topic
				$result = $db->query('SELECT posted FROM '.$db->prefix.'posts WHERE topic_id='.$tid.' ORDER BY id DESC LIMIT 1, 1')
					 or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
				$previous_post_time = intval($db->result($result));

				// Get any subscribed users that should be notified (banned users are excluded)
				$result = $db->query('SELECT u.id, u.email, u.notify_with_post, u.language 
					FROM '.$db->prefix.'users AS u 
						INNER JOIN '.$db->prefix.'subscriptions AS s ON u.id=s.user_id 
						LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id='.$cur_posting['id'].' AND fp.group_id=u.group_id) 
						LEFT JOIN '.$db->prefix.'online AS o ON u.id=o.user_id 
						LEFT JOIN '.$db->prefix.'bans AS b ON u.username=b.username 
					WHERE b.username IS NULL 
						AND COALESCE(o.logged, u.last_visit) > '.$previous_post_time.' 
						AND (fp.read_forum IS NULL OR fp.read_forum=1) 
						AND s.topic_id='.$tid.' 
						AND u.id!='.intval($pun_user['id']))
					or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());

//				echo $db->num_rows($result); exit('--SELECT u.id, u.email, u.notify_with_post, u.language FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'subscriptions AS s ON u.id=s.user_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id='.$cur_posting['id'].' AND fp.group_id=u.group_id) LEFT JOIN '.$db->prefix.'online AS o ON u.id=o.user_id LEFT JOIN '.$db->prefix.'bans AS b ON u.username=b.username WHERE b.username IS NULL AND COALESCE(o.logged, u.last_visit)>'.$previous_post_time.' AND (fp.read_forum IS NULL OR fp.read_forum=1) AND s.topic_id='.$tid.' AND u.id!='.intval($pun_user['id']));
				if ($db->num_rows($result))
				{
					require_once PUN_ROOT.'include/email.php';

					$notification_emails = array();

					// Loop through subscribed users and send e-mails
					while ($cur_subscriber = $db->fetch_assoc($result))
					{
//						print_d($cur_subscriber); exit();
						// Is the subscription e-mail for $cur_subscriber['language'] cached or not?
						if (!isset($notification_emails[$cur_subscriber['language']]))
						{
							if (file_exists(PUN_ROOT.'lang/'.$cur_subscriber['language'].'/mail_templates/new_reply.tpl'))
							{
								// Load the "new reply" template
								$mail_tpl = trim(file_get_contents(PUN_ROOT.'lang/'.$cur_subscriber['language'].'/mail_templates/new_reply.tpl'));

								// Load the "new reply full" template (with post included)
								$mail_tpl_full = trim(file_get_contents(PUN_ROOT.'lang/'.$cur_subscriber['language'].'/mail_templates/new_reply_full.tpl'));

								// The first row contains the subject (it also starts with "Subject:")
								$first_crlf = strpos($mail_tpl, "\n");
								$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
								$mail_message = trim(substr($mail_tpl, $first_crlf));

								$first_crlf = strpos($mail_tpl_full, "\n");
								$mail_subject_full = trim(substr($mail_tpl_full, 8, $first_crlf-8));
								$mail_message_full = trim(substr($mail_tpl_full, $first_crlf));

								$mail_subject = str_replace('<topic_subject>', '\''.$cur_posting['subject'].'\'', $mail_subject);
								$mail_message = str_replace('<topic_subject>', '\''.$cur_posting['subject'].'\'', $mail_message);
								$mail_message = str_replace('<replier>', $username, $mail_message);
								$mail_message = str_replace('<post_url>', $pun_config['o_base_url'].'/viewtopic.php?pid='.$new_pid.'#p'.$new_pid, $mail_message);
								$mail_message = str_replace('<unsubscribe_url>', $pun_config['o_base_url'].'/misc.php?unsubscribe='.$tid, $mail_message);
								$mail_message = str_replace('<board_mailer>', $pun_config['o_board_title'].' '.$lang_common['Mailer'], $mail_message);

								$mail_subject_full = str_replace('<topic_subject>', '\''.$cur_posting['subject'].'\'', $mail_subject_full);
								$mail_message_full = str_replace('<topic_subject>', '\''.$cur_posting['subject'].'\'', $mail_message_full);
								$mail_message_full = str_replace('<replier>', $username, $mail_message_full);
								$mail_message_full = str_replace('<message>', $message, $mail_message_full);
								$mail_message_full = str_replace('<post_url>', $pun_config['o_base_url'].'/viewtopic.php?pid='.$new_pid.'#p'.$new_pid, $mail_message_full);
								$mail_message_full = str_replace('<unsubscribe_url>', $pun_config['o_base_url'].'/misc.php?unsubscribe='.$tid, $mail_message_full);
								$mail_message_full = str_replace('<board_mailer>', $pun_config['o_board_title'].' '.$lang_common['Mailer'], $mail_message_full);

								$notification_emails[$cur_subscriber['language']][0] = $mail_subject;
								$notification_emails[$cur_subscriber['language']][1] = $mail_message;
								$notification_emails[$cur_subscriber['language']][2] = $mail_subject_full;
								$notification_emails[$cur_subscriber['language']][3] = $mail_message_full;

								$mail_subject = $mail_message = $mail_subject_full = $mail_message_full = null;
							}
						}

//						print_d($notification_emails); print_d($cur_subscriber); exit();
						// We have to double check here because the templates could be missing
						if (isset($notification_emails[$cur_subscriber['language']]))
						{
/*							if ($cur_subscriber['notify_with_post'] == '0')
								pun_mail($cur_subscriber['email'], $notification_emails[$cur_subscriber['language']][0], $notification_emails[$cur_subscriber['language']][1]);
							else
								pun_mail($cur_subscriber['email'], $notification_emails[$cur_subscriber['language']][2], $notification_emails[$cur_subscriber['language']][3]);
*/
							bors_mailing::add($post->topic(), $cur_subscriber['id']);
						}

					}
				}
			}

			$is_new_topic = false;
		}
		// If it's a new topic
		else if ($fid)
		{
			// Create the topic
			$db->query('INSERT INTO '.$db->prefix.'topics (poster, subject, description, posted, last_post, last_poster, forum_id) VALUES(\''.$db->escape($username).'\', \''.$db->escape($subject).'\', \''.$db->escape($description).'\', '.$now.', '.$now.', \''.$db->escape($username).'\', '.$fid.')') or error('Unable to create topic', __FILE__, __LINE__, $db->error());
			$new_tid = $db->insert_id();

			if (!$pun_user['is_guest'])
			{
				// Create the post ("topic post")
//				$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_id, poster_ip, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', '.$pun_user['id'].', \''.get_remote_address().'\', \''.$hide_smilies.'\', '.$now.', '.$new_tid.')') or error('Unable to create post', __FILE__, __LINE__, $db->error());
				$tdb = new driver_mysql(config('punbb.database'));
				$data = array(
					'poster' => $username, 
					'poster_id' => $pun_user['id'], 
					'poster_ip' => get_remote_address(), 
					'poster_ua' => @$_SERVER['HTTP_USER_AGENT'],
					'hide_smilies' => $hide_smilies, 
					'posted' => $now, 
					'topic_id' => $new_tid,
					'answer_to_post_id' => $qid,
					'answer_to_user_id' => $answer_to_post ? $answer_to_post->owner_id() : 0,
					'source' => $message,
				);

				$tdb->insert('posts', $data);
				$data['id'] = $new_pid = $tdb->last_id();
				$tdb->close();

				// To subscribe or not to subscribe, that ...
				if ($pun_config['o_subscriptions'] == '1' && (isset($_POST['subscribe']) && $_POST['subscribe'] == '1'))
					$db->query('INSERT INTO '.$db->prefix.'subscriptions (user_id, topic_id) VALUES('.$pun_user['id'].' ,'.$new_tid.')') or error('Unable to add subscription', __FILE__, __LINE__, $db->error());
			}
			else
			{
				// Create the post ("topic post")
				$tdb = new DataBase(config('punbb.database'));
				$data = array(
					'poster' => $username, 
					'poster_ip' => get_remote_address(), 
					'poster_ua' => @$_SERVER['HTTP_USER_AGENT'],
					'poster_email' => ($pun_config['p_force_guest_email'] == '1' || $email != '') ? $email : '',
					'hide_smilies' => $hide_smilies, 
					'posted' => $now, 
					'topic_id' => $new_tid,
					'answer_to_post_id' => $qid,
					'answer_to_user_id' => $answer_to_post ? $answer_to_post->owner_id() : 0,
					'source' => $message,
				);

				$tdb->insert('posts', $data);
				$data['id'] = $new_pid = $tdb->last_id();
//				$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_ip, poster_email, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', \''.get_remote_address().'\', '.$email_sql.', \''.$hide_smilies.'\', '.$now.', '.$new_tid.')') or error('Unable to create post', __FILE__, __LINE__, $db->error());
				$tdb->close();
			}

			$cms_db->update('topics', array('id' => $new_tid), array(
				'first_pid' => $new_pid,
				'poster_id' => $pun_user['id'],
			));

			// Update the topic with last_post_id

			$db->query('UPDATE '.$db->prefix.'topics SET last_post_id='.$new_pid.' WHERE id='.$new_tid) 
				or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			update_forum($fid);

			include_once("engines/bors.php");
			$topic = bors_load('balancer_board_topic', $new_tid, array('no_load_cache' => true));
			$topic->recalculate(false);
			$is_new_topic = true;
			$post  = bors_load('balancer_board_post',  $new_pid, array('no_load_cache' => true));
		}

		// Этот блок держать над пересчётами данных топика, чтобы аттачи в них уже учитывались.
		// Attachment Mod Block Start
		if(isset($_FILES['attached_file'])
				&& $_FILES['attached_file']['size'] !=0
				&& is_uploaded_file($_FILES['attached_file']['tmp_name'])
		)
			if(!attach_create_attachment($_FILES['attached_file']['name'],$_FILES['attached_file']['type'], $_FILES['attached_file']['size'],$_FILES['attached_file']['tmp_name'],$new_pid,count_chars($message)))
				error('Error creating attachment, inform the owner of this bulletin board of this problem. (Most likely something to do with rights on the filesystem)',__FILE__,__LINE__);
		// Attachment Mod Block End

		if(!empty($_POST['keywords_string']))
			$topic->set_keywords_string($_POST['keywords_string'], true);

		$topic->set_modify_time(time());
		$topic->set_last_post_create_time($post->create_time());
		$topic->topic_updated($post, $was_notified);

		$post->set_modify_time(time(), true);
		$post->parents_answers_recount(0);

		if($me->xmpp_notify_enabled() && $me->id() != $post->owner_id())
			bors_messages_users_xmpp::queue($me, $topic);

		if($me && $me->is_coordinator())
		{
			if(!empty($_POST['is_moderatorial']))
			{
				$post->set_is_moderatorial(1, true);
				balancer_board_action::add($topic, "Административное предупреждение: {$post->nav_named_link()}", true);
			}
		}

		if($is_new_topic)
			$topic_page = 1;
		else
			$topic_page = intval($topic->num_replies()/$topic->items_per_page()) + 1;

		$post->set_topic_page($topic_page, true);
		$post->answer_to_user_id(); // Читаем, чтобы обновися кеш

		$topic->store();
		$post->store();

		$topic->set_page($page);

/*
		$ldtext = to_translit($topic->title());
		$ldtext = preg_replace('/\W/', ' ', $ldtext);
		$ldtext = str_replace(' ', '-', trim(substr(trim(preg_replace('/\s+/', ' ', $ldtext)), 0, 16))).'>';
//		$ldtext .= substr(to_translit($post->author_name()), 0, 8)."> ";
		$ldtext2 = substr(str_replace("\n", " ", to_translit($post->snip())) , 0, 100);
		$ldtext2 = preg_replace("/[^\w']/", ' ', $ldtext2);
		$ldtext2 = trim(preg_replace('/\s+/', ' ', $ldtext2));
//		@file_get_contents('http://home.balancer.ru/lorduino/arduino.php?text='.urlencode($ldtext.$ldtext2));
		@file_put_contents('/tmp/ldtext.txt', $ldtext);
*/

		$topic->cache_clean();
		$post->cache_clean();

		// If the posting user is logged in, increment his/her post count
		if (!$pun_user['is_guest'])
		{
			$low_prio = '';//($db_type == 'mysql') ? 'LOW_PRIORITY ' : '';
			$db->query('UPDATE '.$low_prio.$db->prefix.'users SET num_posts=num_posts+1, last_post='.$now.' WHERE id='.$pun_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
		}

/*
		if($post->owner()->num_posts() < 20 && $post->owner()->create_time() > time() - 7*86400)
		{
			$post->set_is_spam(balancer_akismet::factory()->classify($post) ? 1 : 0, true);
			if($post->is_spam())
			{
				debug_hidden_log('spam', "Marked as spam: [owner={$post->owner()}, num_posts={$post->owner()->num_posts()}, registered={$post->owner()->create_time()}]".$post->source());
//				message('Ваше сообщение похоже на спам. Оно оставлено на проверку координаторам. Если сообщение корректно, оно будет размещено на форуме');
			}
		}
*/

		// Если эту фигню удалять, то надо проверить на аттачи и множественные аттачи, как при постинге, так и при редактировании
		// Вызывать после добавления аттачей выше.
		$post->recalculate($topic);
		$post->full_recalculate_and_clean();

		if(!empty($_POST['as_blog']) && !$post->get('is_spam'))
			$blog = balancer_board_blog::create($post, @$_POST['keywords']);

		if(!empty($_POST['is_translate']) && !$post->get('is_spam'))
		{
			include_once('engines/blogs/livejournal.com.php');
			bors_blog_livejournal_com_post(
				$post->owner_id(),
				$topic,
				$topic->first_post()->id() == $post->id() ? $topic : $post,
				$post,
				empty($_POST['keywords']) ? $topic : $blog
			);
		}

		//	function add_money($amount, $action=NULL, $comment=NULL, $object=NULL, $source=NULL)
		bors()->user()->add_money(-2, 'post', "Сообщение", $post);

		require_once('inc/navigation.php');
		unset($_SERVER['QUERY_STRING']);

//		go("http://forums.balancer.ru/posts/{$post->id()}/process");
		go($post->url_in_topic(NULL, true));
		pun_exit();
	}
}

// Если форумы в R/O, то на соответствующую страницу. Редирект тут, чтобы
// при ответе оный не пропадал.
if(bors_var::get('r/o-by-move-time-'.$forum->category_id()) > time())
{
	header("Status: 302 Moved Temporarily");
	header("Location: http://ls.balancer.ru/blog/airbase/111.html");
	bors_exit();
}

// If a topic id was specified in the url (it's a reply).
if ($tid)
{
	$action = $lang_post['Post a reply'];
//	$form = '<form id="post" method="post" action="post.php?action=post&amp;tid='.$tid.'" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">';

	$topic = bors_load('balancer_board_topic', $tid);

	$form = "";
//	if($topic->num_replies() >= 500)
//		$form = "<p style=\"color: red; font-size: 10pt; font-weight: 900; padding: 10px;\">Внимание! Слишком большой топик! Рекомендуется использовать ответ в новый топик путём установки отметки «<i>Разместить ответ как новую тему (требуется ввести заголовок)</i>» под формой ответа</p>";

	$form .= '<form id="post" method="post" enctype="multipart/form-data" action="post.php?action=post&amp;tid='.$tid.'" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">'; //Attachment Mod has added enctype="multipart/form-data"

	// If a quote-id was specified in the url.
	if($qid)
	{
		$post = bors_load('balancer_board_post', $qid);
		$q_poster  = $post ? $post->author_name() : ec("Ошибка сообщения $qid");
		$q_message = $post ? $post->source() : ec("Ошибка сообщения $qid");

		$q_message = str_replace('[img]', '[url]', $q_message);
		$q_message = str_replace('[/img]', '[/url]', $q_message);
		$q_message = pun_htmlspecialchars(strip_tags($q_message));

		include_once('inc/design/make_quote.php');

		$joke = object_property($post, 'joke_owner');
		if($quoted_post /*&& !$quoted_post->is_hidden()*/ && !$quoted_post->is_deleted())
			$quote = make_quote($joke ? $joke->title() : $q_poster, $q_message)."\n";
		else
			$quote = '';
	}

	$forum_name = "<a href=\"{$pun_config['root_uri']}/viewforum.php?id={$cur_posting['id']}\">".pun_htmlspecialchars($cur_posting['forum_name']).'</a>';
}
// If a forum_id was specified in the url (new topic).
else if ($fid)
{
	$action = $lang_post['Post new topic'];
//	$form = '<form id="post" method="post" action="post.php?action=post&amp;fid='.$fid.'" onsubmit="return process_form(this)">';
	$form = '<form id="post" method="post" enctype="multipart/form-data" action="post.php?action=post&amp;fid='.$fid.'" onsubmit="return process_form(this)">';		//Attachment Mod has added enctype="multipart/form-data"

	$forum_name = pun_htmlspecialchars($cur_posting['forum_name']);
}
else
	message($lang_common['Bad request']);

$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$action;
$required_fields = array('req_email' => $lang_common['E-mail'], 'req_subject' => $lang_common['Subject'], 'req_message' => $lang_common['Message']);
$focus_element = array('post');

if (!$pun_user['is_guest'])
	$focus_element[] = ($fid) ? 'req_subject' : 'req_message';
else
{
	$required_fields['req_username'] = $lang_post['Guest name'];
	$focus_element[] = 'req_username';
}

//Attachment Mod Block Start
//Fetch some stuff so we know if the user is allowed to attach files to the post ... oh and preview won't work... I'm not going to add shitload of stuff to get some temporary upload area ;)

$attach_allowed = $pun_user['g_id'] != PUN_GUEST;

//Attachment Mod Block End

$header[] = "<script type=\"text/javascript\" src=\"/_bors3rdp/js/flowplayer-3.2.12/flowplayer-3.2.11.min.js\"></script>";
include('include/tinymce.php');
require PUN_ROOT.'header.php';

?>

<div class="linkst">
	<div class="inbox">
		<ul><li><a href="<?php echo $pun_config['root_uri'];?>/index.php"><?= $lang_common['Index'] ?></a></li>
			<li>&nbsp;&raquo;&nbsp;<?= $forum_name ?></li>
			<?php
				if(isset($cur_posting['subject']))
					echo '<li>&nbsp;&raquo;&nbsp;'.$topic->titled_link().'</li>';
			?>
		</ul>
	</div>
</div>

<?php

// If there are errors, we display them
if (!empty($errors))
{

?>
<div id="posterror" class="block">
	<h2><span><?php echo $lang_post['Post errors'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<p><?php echo $lang_post['Post errors info'] ?></p>
			<ul>
<?php

	while (list(, $cur_error) = each($errors))
		echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
			</ul>
		</div>
	</div>
</div>

<?php

}
else if (isset($_POST['preview']))
{
	config_set('cache_disabled', true);
	require_once PUN_ROOT.'include/parser.php';
	$preview_message = parse_message($message, $hide_smilies, true);

?>
<div id="postpreview" class="blockpost">
	<h2><span><?php echo $lang_post['Post preview'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postright">
				<div class="postmsg">
					<?php echo $preview_message."\n" ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

}


$cur_index = 1;

if($true_text <= 40)
{
	$true_text = sprintf("%.1f", $true_text);
	echo "<div class=\"red-box\">В Вашем сообщении только {$true_text}% введённого Вами текста. Это очень мало
и похоже на избыточное цитирование больших объёмов текста. Или сократите цитаты до необходимого
минимума, или подтвердите согласие с избыточным цитированием установив метку «<b>Я подтверждаю избыточное цитирование</b>»
в части окна под полем ввода текста и редактирования. В этом случае, если избыточное
цитирование было необоснованным, модераторы сайта могут выставить Вам штрафной балл.
Посмотреть подробности и задать вопросы можно по ссылке 
«<a href=\"http://www.balancer.ru/support/2009/07/t67978--ob-izbytochnom-tsitirovanii.195.html\">Об избыточном цитировании</a>».
</div><br/>";
}

if($messages_limit >= 0)
{
	echo "<div class=\"red-box\">Из-за активных штрафов число Ваших сообщений в одном форуме в сутки ограничено {$messages_limit}-ю. 
	Всего за последние 24 часа в этом форуме Вы отправили {$today_posted} сообщени".sklon($today_posted, 'е,я,й').". ";

	if($messages_rest <= 0)
		echo "Вы не можете больше отправить ни одного сообщения сюда до <b>".full_time($me->next_can_post($messages_limit, $forum_id))."</b>. ";
	else
		echo "Вы можете отправить ещё {$messages_rest}. ";

	echo "Подробности в теме «<a href=\"http://www.balancer.ru/support/2009/07/t67998--ogranichenie-sutochnogo-chisla-soobschenij-dlya-polzovatelej.1757.html\">Ограничение суточного числа сообщений</a>»";
	echo "</div><br />";
}

if(($warn_count = $me->warnings()) > 0)
{
	echo "<div class=\"red-box\">";
	echo "У Вас ".sklon($warn_count,"активен","активны","активны")." $warn_count ".sklon($warn_count, "общий штраф", "общих штрафа", "общих штрафов");
	echo " и ".$me->warnings_in($forum->id())." в текущем форуме. ";
	echo "При достижении 10 общих штрафов, Вы будете автоматически переведены в режим \"только чтение\" на срок до истечения самого старого из активных штрафов (срок их активности - две недели с момента выставления) во всех форумах. ";
	echo "При достижении 5 активных штрафов в данном форуме Вы автоматически будете лишены возможности писать в него, но будете иметь возможность писать в другие. ";
	echo "Посмотреть список своих штрафов Вы можете на <a href=\"http://www.balancer.ru/users/{$me->id()}/warnings/\">странице Ваших штрафов</a>. ";
	echo "Подробности в теме «<a href=\"http://www.balancer.ru/support/2009/07/t68005--poforumnye-ogranicheniya-5-shtrafov.4435.html\">Пофорумные ограничения</a>»"
		.'<br/><br/><b>'.bbf_bans::message_ls().'</b>';
	echo "</div><br/>";
}

if($topic)
{
	$moved_topics = bors_find_all('balancer_board_topic', [
		'*set' => 'COUNT(*) AS num_moved_posts',
		'inner_join' => 'balancer_board_post ON balancer_board_topic.id = balancer_board_post.topic_id',
		'balancer_board_post.create_time>' => time()-183*86400,
		'original_topic_id' => $topic->id(),
		'topic_id NOT IN' => [59483/*Мусор*/],
		'group' => 'balancer_board_topic.id',
		'order' => 'COUNT(*) DESC',
		'limit' => 10,
	]);

	$moved_topics_html = [];
	foreach($moved_topics as $t)
	{
		$desc = preg_replace('/Перенос из темы.+?»/u', '', $t->description());
/*
		if($t->answer_notice())
		{
			if($desc)
				$desc .= '. ';
			$desc .= 'x'.preg_replace('/^([^\.]+?).*$/', '$1', $t->answer_notice());
		}
*/
		$moved_topics_html[] = "<li>&nbsp;&middot;&nbsp;<a href=\"{$t->url_ex('new')}\">{$t->title()}</a>".($desc ? " ({$desc})":'')." [{$t->num_moved_posts()}]</li>";
	}

	if($moved_topics_html)
		$moved_topics_html = "<p><b>Больше всего переносов за последнее время из этой темы было в следующие:</b><ul>".join("\n", $moved_topics_html)."</ul></p>";
	else
		$moved_topics_html = "";

	if($topic->answer_notice())
		echo "
			<div class=\"alert alert-error\" style=\"padding: 4px; margin-bottom: 4px;\">
				<b style=\"color: red\">Внимание!</b> В эту тему пишут ".lcml_bbh($topic->answer_notice())."
				Если Ваше сообщение не отвечает данной тематике, то
				сообщение может быть перенесено в более подходящую
				тему а Вам выставлен штраф за офтопик или некорректный
				выбор темы.
				{$moved_topics_html}
			</div>
		";
	elseif($moved_topics_html)
	{
		echo "
			<div class=\"alert alert-warning\" style=\"padding: 4px; margin-bottom: 4px;\">
				Обнаружено несколько переносов из этой темы в другие.
				Посмотрите внимательно, не является ли одна из них более
				подходящей для вашего сообщения?
				{$moved_topics_html}
			</div>
		";
	}
}
?>

<div class="blockform">
	<h2><span><?php echo $action ?></span></h2>
	<div class="box">
		<?php echo $form."\n" ?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Write message legend'] ?></legend>
					<div class="infldset txtarea">
						<input type="hidden" name="form_sent" value="1" />
<?php

if ($pun_user['is_guest'])
{
	$email_label = ($pun_config['p_force_guest_email'] == '1') ? '<strong>'.$lang_common['E-mail'].'</strong>' : $lang_common['E-mail'];
	$email_form_name = ($pun_config['p_force_guest_email'] == '1') ? 'req_email' : 'email';

?>						<label class="conl"><strong><?php echo $lang_post['Guest name'] ?></strong><br /><input type="text" name="req_username" value="<?php if (isset($_POST['req_username'])) echo pun_htmlspecialchars($username); /*"*/ ?>" size="25" maxlength="25" tabindex="<?php echo $cur_index++; ?>" /><br /></label>
						<label class="conl"><?php echo $email_label; ?><br /><input type="text" name="<?php echo $email_form_name ?>" value="<?php if (isset($_POST[$email_form_name])) echo pun_htmlspecialchars($email); ?>" size="50" maxlength="50" tabindex="<?php echo $cur_index++; /*"*/?>" /><br /></label>
						<div class="clearer"></div>
<?php

}

if ($fid): ?>
						<label><strong><?php echo $lang_common['Subject'] ?></strong><br /><input class="longinput" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo pun_htmlspecialchars($subject); ?>" size="80" maxlength="255" tabindex="<?php echo $cur_index++; /*"*/?>" /><br /></label>
						<label>Подзаголовок (описание темы, не обязательно)<br /><input class="longinput" type="text" name="nreq_description" value="<?php if (isset($_POST['nreq_description'])) echo pun_htmlspecialchars($description); ?>" size="80" maxlength="255" tabindex="<?php echo $cur_index++; ?>" /><br /></label>
						<label>Ключевые слова (через запятую)<br/><input class="longinput" type="text" name="keywords_string" size="80" maxlength="255" tabindex="<?php echo $cur_index++ ?>" value="<?php echo pun_htmlspecialchars(isset($_POST['keywords_string']) || !$forum ? $_POST['keywords_string'] : $forum->keywords_string()) ?>" /><br /></label>
<?php else: ?>
						<div id="here_keywords"></div>
						<div id="here_subject"></div>
<?php endif; ?>
						<label><strong><?php echo $lang_common['Message'] ?></strong><br />
<?php
$profile = config('client_profile');
if(!$profile || $profile->textarea_type() == 'markitup')
{?>
<div id="emoticons">
	<a href="#" title=":)"><img src="http://www.airbase.ru/forum/smilies/smile.gif" /></a>
	<a href="#" title=":("><img src="http://www.airbase.ru/forum/smilies/frown.gif" /></a>
	<a href="#" title=":eek:"><img src="http://www.airbase.ru/forum/smilies/eek.gif" /></a>
	<a href="#" title=":p"><img src="http://www.airbase.ru/forum/smilies/tongue.gif" /></a>
	<a href="#" title=";)"><img src="http://www.airbase.ru/forum/smilies/wink.gif" /></a>
	<a href="#" title=":D"><img src="http://www.airbase.ru/forum/smilies/biggrin.gif" /></a>
</div>
<?php } ?>
						<textarea name="req_message" id="bbcode" rows="20" cols="95" tabindex="<?php echo $cur_index++ ?>"><?php echo isset($_POST['req_message']) ? pun_htmlspecialchars($message) : (isset($quote) ? $quote : ''); ?></textarea><br /></label>
						<ul class="bblinks">
							<li><a href="<?php echo $pun_config['root_uri'];?>/help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang_common['BBCode'] ?></a>: <?php echo ($pun_config['p_message_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="<?php echo $pun_config['root_uri'];?>/help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang_common['img tag'] ?></a>: <?php echo ($pun_config['p_message_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="<?php echo $pun_config['root_uri'];?>/help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang_common['Smilies'] ?></a>: <?php echo ($pun_config['o_smilies'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
						</ul>
					</div>
				</fieldset>
<?php

//Attachment Mod Block Start
if($attach_allowed){
?>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_attach['Attachment'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<input type="hidden" name="MAX_FILE_SIZE" value="<?=config('forum_attach_max_size')?>" /><input type="file" name="attached_file" size="80" tabindex="<?php echo $cur_index++ ?>" /><br />
							<?php echo $lang_attach['Note'];/*"*/ ?>
						</div>
					</div>
				</fieldset>
<?php
}
//Attachment Mod Block End

$checkboxes = array();
if (!$pun_user['is_guest'])
{
	if ($pun_config['o_smilies'] == '1')
		$checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'"'.(isset($_POST['hide_smilies']) ? ' checked="checked"' : '').' />'.$lang_post['Hide smilies'];

	if ($pun_config['o_subscriptions'] == '1')
//		$checkboxes[] = '<label><input type="checkbox" name="subscribe" value="1" tabindex="'.($cur_index++).'" checked="checked" />'.$lang_post['Subscribe'];
		$checkboxes[] = '<label><input type="checkbox" name="subscribe" value="1" tabindex="'.($cur_index++).'"'.(isset($_POST['subscribe']) || empty($_GET['tid']) ? ' checked="checked"' : '').' />'.$lang_post['Subscribe'];

//	$checkboxes[] = "<label><input type=\"checkbox\" name=\"as_new_post\" value=\"1\" tabindex=\"".($cur_index++).'"'.(isset($_POST['as_new_post']) ? ' checked="checked"' : '')." onClick=\"getElementById('here_subject').innerHTMLval = this.checked ? '' : '".addslashes("<label><strong>Заголовок</strong><br /><input class=\"longinput\" type=\"text\" name=\"req_subject\" value=\"\" size=\"80\" maxlength=\"255\" tabindex=\"1\" /><br /></label>")."'\"/>Разместить ответ как новую тему (требуется ввести заголовок)";

	if($tid) // Ответ
	{
		$checkboxes[] = "<label><input type=\"checkbox\" name=\"as_blog\"     value=\"1\" tabindex=\"".($cur_index++).'"'.(isset($_POST['as_blog'])     ? ' checked="checked"' : '')." onClick=\"getElementById('here_keywords').innerHTML= this.checked ? '".addslashes("<label><strong>Теги:</strong>&nbsp;<input                     class='longinput' type='text' name='keywords'    value='".defval($_POST, 'keywords', $topic->keywords_string())."'    size='40' maxlength='255' /><br /></label>")."' : ''\"/>Разместить ответ в <a href=\"http://www.balancer.ru/user/{$pun_user['id']}/blog/\">Вашем блоге</a>";
		$checkboxes[] = "<label><input type=\"checkbox\" name=\"is_translate\"     value=\"1\" tabindex=\"".($cur_index++).'"'.(isset($_POST['as_blog'])     ? ' checked="checked"' : '')." onClick=\"getElementById('here_keywords').innerHTML= this.checked ? '".addslashes("<label><strong>Теги:</strong>&nbsp;<input                     class='longinput' type='text' name='keywords' size='40' maxlength='255' /><br /></label>")."' : ''\"/>Транслировать ответ в ЖЖ";
		$checkboxes[] = "<label><input type=\"checkbox\" name=\"as_new_post\" value=\"1\" tabindex=\"".($cur_index++).'"'.(isset($_POST['as_new_post']) ? ' checked="checked"' : '')." onClick=\"getElementById('here_subject').innerHTML = this.checked ? '".addslashes("<label><strong>{$lang_common['Subject']}</strong><br /><input class='longinput' type='text' name='req_subject' value='".(@$_POST['req_subject'])."' size='80' maxlength='255' /><br /></label>")."' : ''\"/>Разместить ответ как новую тему (требуется ввести заголовок)";
	}
	else
	{
		$checkboxes[] = "<label><input type=\"checkbox\" name=\"as_blog\" value=\"1\" tabindex=\"".($cur_index++)."\" checked=\"checked\" />Разместить тему в <a href=\"http://www.balancer.ru/user/{$pun_user['id']}/blog/\">Вашем блоге</a>";
		$checkboxes[] = "<label><input type=\"checkbox\" name=\"is_translate\"     value=\"1\" tabindex=\"".($cur_index++).'"'.(isset($_POST['as_blog'])     ? ' checked="checked"' : '')." onClick=\"getElementById('here_keywords').innerHTML= this.checked ? '".addslashes("<label><strong>Теги:</strong>&nbsp;<input                     class='longinput' type='text' name='keywords' size='40' maxlength='255' /><br /></label>")."' : ''\"/>Транслировать тему в ЖЖ";
	}
}
else if ($pun_config['o_smilies'] == '1')
	$checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'"'.(isset($_POST['hide_smilies']) ? ' checked="checked"' : '').' />'.$lang_post['Hide smilies'];

if($true_text <= 40)
	$checkboxes[] = "<label><input type=\"checkbox\" name=\"overquote_confirmed\" value=\"1\" tabindex=\"".($cur_index++)."\"/>Я подтверждаю избыточное цитирование</label>";

if($me && $me->is_coordinator())
	$checkboxes[] = "<label style=\"color:red\"><input type=\"checkbox\" name=\"is_moderatorial\" value=\"1\" tabindex=\"".($cur_index++)."\" />Данное сообщение - модераториал</label>";

if (!empty($checkboxes))
{

?>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Options'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<?php echo implode('<br /></label>'."\n\t\t\t\t", $checkboxes).'<br /></label>'."\n" ?>
						</div>
					</div>
				</fieldset>
<?php

}

if($qid) echo "<input type=\"hidden\" name=\"qid\" value=\"$qid\">\n";
?>			</div>
<div class="yellow_box">
<span class="red b">Важно!</span> Размещая на форуме информацию, авторами которой Вы <b>не</b> являетесь,
Вы обязуетесь нести всю ответственность за соблюдение прав авторов
этой информации. В случае претензий правообладателей эта информация
может быть удалена. Все материалы, автором которых являетесь Вы,
при опубликовании на форумах приобретают лицензию <a href="http://www.balancer.ru/support/2009/02/t66269--Prava-publikatsii-avtorskikh-materialov-.html"><b>Creative Commons</b> (by-nc-sa)</a>.
<br/><b>Новое (13.11.2013):</b> Также Вы обязуетесь в своих сообщениях соблюдать законы Российской Федерации и нести ответственность
за их нарушение. В случае подобных нарушений вся Ваша переписка может быть удалена,
а информация о Вас предоставлена органам защиты правопорядка.
</div>
			<p><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /><input type="submit" name="preview" value="<?php echo $lang_post['Preview'] ?>" tabindex="<?php echo $cur_index++ /*"*/?>" accesskey="p" /><a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
	</div>
</div>

<?php

// Check to see if the topic review is to be displayed.
if ($tid && $pun_config['o_topic_review'] != '0')
{
?>

<div id="postreview" class="blockpost">
	<h2><span><?php echo $lang_post['Topic review'] ?></span></h2>
<?php

	//Set background switching on
	$bg_switch = true;
	$post_count = 0;

	foreach(bors_find_all('forum_post', array('topic_id' => $tid, 'order' => '-id', 'limit' => $pun_config['o_topic_review'])) as $preview_post)
	{
		// Switch the background color for every message.
		$bg_switch = ($bg_switch) ? $bg_switch = false : $bg_switch = true;
		$vtbg = ($bg_switch) ? ' roweven' : ' rowodd';
		$post_count++;

?>
	<div class="box<?php echo $vtbg ?>/*"*/">
		<div class="inbox">
			<div class="postleft">
				<dl>
					<dt><strong><?php echo pun_htmlspecialchars($preview_post->author_name()) ?></strong></dt>
					<dd><?php echo format_time($preview_post->create_time()) ?></dd>
				</dl>
			</div>
			<div class="postright">
				<div class="postmsg">
					<tt><?php echo str_replace("\n", "<br/>\n", htmlspecialchars($preview_post->source()));?></tt>
				</div>
			</div>
			<div class="clearer"></div>
		</div>
	</div>
<?php

	}

?>
</div>
<?php

}

require 'footer.php';
