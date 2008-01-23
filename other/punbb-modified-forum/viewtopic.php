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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
include_once("funcs/DataBase.php");
$cms_db = &new DataBase('punbb');

$archive_loaded = false;

define('PUN_ROOT', './');

// If a post ID is specified we determine topic ID and page number so we can redirect to the correct message
if($pid)
{
	$id = intval($cms_db->get("SELECT topic_id FROM posts WHERE id=$pid"));
	if(!$id)
	{
		for($i=0; $i<10; $i++)
			if($id = intval($cms_db->get("SELECT topic_id FROM posts_archive_{$i} WHERE id={$pid}")))
				break;
	}
	
	if(!$id)
	{
		require PUN_ROOT.'include/common.php';
		message($lang_common['Bad request']);
	}
	
	// Determine on what page the post is located (depending on $pun_user['disp_posts'])
	$posts = $cms_db->get_array("SELECT id FROM posts WHERE topic_id=$id ORDER BY posted");
	if(sizeof($posts) == 0)
	{
		$cms_db->query("INSERT IGNORE posts SELECT * FROM posts_archive_".($id%10)." WHERE topic_id = $id");
		$archive_loaded = true;
		$posts = $cms_db->get_array("SELECT id FROM posts WHERE topic_id=$id ORDER BY posted");
	}

	for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
		if ($posts[$i] == $pid)
		{
			$_GET['p'] = intval( $i / 25) + 1;
			break;
		}
}

$sub_id	= $id % 1000;

$_SERVER['REQUEST_URI'] = "/forum/topic/$sub_id/$id".(empty($_GET['p'])||$_GET['p']==1 ? "":",{$_GET['p']}")."/";

include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
include_once("funcs/Cache.php");
$GLOBALS['global_cache'] = &new Cache();

require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/attach/attach_incl.php'; //Attachment Mod row, loads variables, functions and lang file

//print_r($GLOBALS['cms']);
if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);

$action = isset($_GET['action']) ? $_GET['action'] : null;
if ($id < 1 && $pid < 1)
	message($lang_common['Bad request']);

// Load the viewtopic.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/topic.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/forum.php';

// If action=new, we redirect to the first new post (if any)
if ($action == 'new' && !$pun_user['is_guest'])
{
	$last_visit = intval($cms_db->get("SELECT last_visit FROM topic_visits WHERE user_id=".intval($pun_user['id'])." AND topic_id=$id"));
	$first_new_post_id = intval($cms_db->get("SELECT MIN(id) FROM {$db->prefix}posts WHERE topic_id=$id AND posted>$last_visit"));
	if(!$first_new_post_id)
		$first_new_post_id = $cms_db->get("SELECT MIN(id) FROM {$db->prefix}posts_archive_".($id%10)." WHERE topic_id=$id AND posted>$last_visit");

	if ($first_new_post_id)
		header("Location: {$pun_config['root_uri']}/viewtopic.php?pid=$first_new_post_id#p$first_new_post_id");
	else	// If there is no new post, we go to the last post
		header("Location: {$pun_config['root_uri']}/viewtopic.php?id=$id&action=last");

	exit;
}

// If action=last, we redirect to the last post
else if ($action == 'last')
{
	for($ii=0; $ii<2; $ii++)
	{
		$last_post_id = $cms_db->get("SELECT MAX(id) FROM {$db->prefix}posts WHERE topic_id=$id");
		if(!$last_post_id)
			$last_post_id = $cms_db->get("SELECT MAX(id) FROM {$db->prefix}posts_archive_".($id%10)." WHERE topic_id=$id");

		if ($last_post_id)
		{
			header("Location: {$pun_config['root_uri']}/viewtopic.php?pid=$last_post_id#p$last_post_id");
			exit;
		}

		$id = intval($cms_db->get("SELECT moved_to FROM topics WHERE id=$id", true, 86400));
	}
}

// Гостей - перекидываем на новый движок
if($pun_user['is_guest'])
{
	SetCookie("cookie_hash", "none", 0, "/");
	$_COOKIE['cookie_hash'] = "none";
	SetCookie("user_id", "0", 0, "/");
	$_COOKIE['user_id'] = "0";

	require_once('funcs/navigation/go.php');

	if($pid)
		go(class_load("forum_post", intval($pid))->url(), true);
	else
		go(class_load("forum_topic", intval($id))->url(max(1, intval(@$_GET['p']))), true);
}

for($ii=0; $ii<2; $i++)
{
	// Fetch some info about the topic
	if (!$pun_user['is_guest'])
		$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, s.user_id AS is_subscribed FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$pun_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') 
			or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
	else
		$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, 0 FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') 
			or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());

	if (!$db->num_rows($result))
	{
		$id = intval($cms_db->get("SELECT moved_to FROM topics WHERE id=$id"));
		if(!$id)
			message($lang_common['Bad request']);
	}
	else
		break;
}

$cur_topic = $db->fetch_assoc($result);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();
$is_admmod = ($pun_user['g_id'] == PUN_ADMIN || ($pun_user['g_id'] == PUN_MOD && array_key_exists($pun_user['username'], $mods_array))) ? true : false;
$is_coordinator = $is_admmod || $pun_user['g_id'] == 5 || $pun_user['g_id'] == PUN_MOD || $pun_user['g_id'] == 21;

// Can we or can we not post replies?
if ($cur_topic['closed'] == '0')
{
	if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod)
		$post_link = "[
			<a href=\"{$pun_config['root_uri']}/post.php?tid=$id\">{$lang_topic['Post reply']}</a> |
			<a href=\"{$pun_config['root_uri']}/post.php?fid={$cur_topic['forum_id']}\">{$lang_forum['Post topic']}</a>
]";
	else
		$post_link = '&nbsp;';
}
else
{
	$post_link = $lang_topic['Topic closed'];

	if ($is_admmod)
		$post_link .= " / <a href=\"{$pun_config['root_uri']}/post.php?tid=$id\">".$lang_topic['Post reply'].'</a>';
}

// Determine the post offset (based on $_GET['p'])
$num_pages = intval($cur_topic['num_replies'] / $pun_user['disp_posts']) + 1;

$p = intval((!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : $_GET['p']);
$start_from = $pun_user['disp_posts'] * ($p - 1);

// Generate paging links
$paging_links = $lang_common['Pages'].': '.paginate($num_pages, $p, "{$pun_config['root_uri']}/viewtopic.php?id=$id");

if ($pun_config['o_censoring'] == '1')
	$cur_topic['subject'] = censor_words($cur_topic['subject']);

$quickpost = false;
if ($pun_config['o_quickpost'] == '1' &&
	!$pun_user['is_guest'] &&
	($cur_topic['post_replies'] == '1' || ($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1')) &&
	($cur_topic['closed'] == '0' || $is_admmod))
{
	$required_fields = array('req_message' => $lang_common['Message']);
	$quickpost = true;
}

if (!$pun_user['is_guest'] && $pun_config['o_subscriptions'] == '1')
{
	if ($cur_topic['is_subscribed'])
		// I apologize for the variable naming here. It's a mix of subscription and action I guess :-)
		$subscraction = '<p class="subscribelink clearb">'.$lang_topic['Is subscribed']." - <a href=\"{$pun_config['root_uri']}/misc.php?unsubscribe=$id\">".$lang_topic['Unsubscribe'].'</a></p>'."\n";
	else
		$subscraction = "<p class=\"subscribelink clearb\"><a href=\"{$pun_config['root_uri']}/misc.php?subscribe=$id\">".$lang_topic['Subscribe'].'</a></p>'."\n";
}
else
	$subscraction = '<div class="clearer"></div>'."\n";

$page_title = pun_htmlspecialchars("{$cur_topic['subject']} / {$pun_config['o_board_title']}");
define('PUN_ALLOW_INDEX', 1);

global $header;
$rss = class_load('forum_topic_rss', $id);
$header[] = "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$rss->url()."\" title=\"Новые сообщения в теме '".$rss->title()."'\" />";

require PUN_ROOT.'header.php';


//$GLOBALS['log_level'] = 10;
//echo $GLOBALS['global_cache']->get("punbb-viewtopics", "$id:$last");

// Increment "num_views" for topic
$low_prio = '';//($db_type == 'mysql') ? 'LOW_PRIORITY ' : '';
$db->query("UPDATE $low_prio {$db->prefix}topics SET num_views=num_views+1, last_access = ".time()." WHERE id=$id") 
	or error('Unable to update topic', __FILE__, __LINE__, $db->error());

if($pun_user['id'] > 1)
{
	$count = intval($cms_db->get("SELECT count FROM topic_visits WHERE user_id=".intval($pun_user['id'])." AND topic_id=".intval($id))) + 1;

	$data = array(
			'topic_id' => $id,
			'user_id' => $pun_user['id'],
			'count' => $count,
			'last_visit' => time(),
		);

	if($count == 1)
		$data['first_visit'] = time();

	$cms_db->store(
		"{$db->prefix}topic_visits", 
		"user_id=".intval($pun_user['id'])." AND topic_id=".intval($id),
		$data,
		false,
		array('priority' => 'low')
	);
}

$ret = $cms_db->get("SELECT last_post, last_edit FROM topics WHERE id=".intval($id));
$last = max($ret['last_post'], $ret['last_edit']);
if($GLOBALS['global_cache']->get("punbb-viewtopics-{$_SERVER['HTTP_HOST']}-{$pun_user['id']}-{$pun_user['style']}-v23", "$id:$last:$p"))
{
	echo $GLOBALS['global_cache']->last();
	$GLOBALS['global_cache'] = NULL;

	// Increment "num_views" for topic
	$low_prio = '';//($db_type == 'mysql') ? 'LOW_PRIORITY ' : '';
	$db->query('UPDATE '.$low_prio.$db->prefix.'topics SET num_views=num_views+1, last_access='.time().' WHERE id='.$id) 
		or error('Unable to update topic', __FILE__, __LINE__, $db->error());

	$forum_id = $cur_topic['forum_id'];
	$footer_style = 'viewtopic';
	require PUN_ROOT.'footer.php';
	return;
}
?>
<script type="text/javascript" src="<? echo $pun_config['root_uri'];?>/js/common.js"></script>

<a name="page_top"></a>
<div class="linkst">
	<div class="inbox">
		<ul><li><a href="<? echo $pun_config['root_uri'];?>/index.php"><?php echo $lang_common['Index'] ?></a></li><li>&nbsp;&raquo;&nbsp;<a href="<? echo $pun_config['root_uri'];?>/viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><?php echo pun_htmlspecialchars($cur_topic['forum_name']) ?></a></li><li>&nbsp;&raquo;&nbsp;<?php echo pun_htmlspecialchars($cur_topic['subject']); /*"*/ ?></li></ul>
		<br/>
		<ul><li>
				<span class="pagelink conl">
					<a href="#page_bottom"><img src="http://balancer.ru/cms/templates/forum/icons/16x16/actions/down.gif" alt="#bottom" border="0"/></a>
					<?php echo $paging_links ?>
				</span>
				<span class="postlink conr"><?php echo $post_link ?></span>
		</li></ul>
		<div class="clearer"></div>
	</div>
</div>

<table border="0" cellSpacing="0" cellPadding="0" class="noborder">
	<tr>
		<td class="topic_left_column" valign="top" style="border-style: none;">
<?
	if(!$pun_user['is_guest'])
		include("design/left.php");
?>
		</td>
		<td class="topic_middle_column" valign="top" style="border-style: none;">		
<?php

require PUN_ROOT.'include/parser.php';

$bg_switch = true;	// Used for switching background color in posts
$post_count = 0;	// Keep track of post numbers

if(!$archive_loaded)
{
	$cnt = $cms_db->get("SELECT COUNT(*) FROM posts WHERE topic_id = $id");
	if(!$cnt || $cnt != $cms_db->get("SELECT COUNT(*) FROM posts_archive_".($id%10)." WHERE topic_id = $id"))
		$cms_db->query("INSERT IGNORE posts SELECT * FROM posts_archive_".($id%10)." WHERE topic_id = $id");
}

// Retrieve the posts
$q = "
	SELECT 
		p.id, 
		p.poster AS username, 
		p.poster_id, 
		p.poster_ip, 
		p.poster_email, 
		p.hide_smilies, 
		p.posted, 
		p.edited, 
		p.edited_by,
		p.answer_to,
		up.posted as up_posted,
		up.poster as up_poster,
		cf.flag
	FROM {$db->prefix}posts AS p
		LEFT JOIN {$db->prefix}posts AS up ON (p.answer_to = up.id)
		LEFT JOIN {$db->prefix}posts_cached_fields AS cf ON (p.id = cf.post_id)
	WHERE p.topic_id=$id 
	ORDER BY p.id 
	LIMIT $start_from, {$pun_user['disp_posts']}";

$result   = $db->query($q, false) 
	or error('Unable to fetch post info', __FILE__, __LINE__, $db->error()); //Attachment Mod, changed the true to false...


include_once($_SERVER['DOCUMENT_ROOT']."/cms/config.php");
include_once("funcs/lcml.php");

while ($cur_post = $db->fetch_assoc($result))
{
//	echo $cur_post['id'].", ";
	if(empty($GLOBALS['bors_data']['cache']['punbb_user'][$cur_post['poster_id']]))
	{
		$poster = $cms_db->get("SELECT * FROM users  WHERE id = ".intval($cur_post['poster_id']));
		$GLOBALS['bors_data']['cache']['punbb_user'][$cur_post['poster_id']] = serialize($poster);
	}
	else
		$poster = unserialize($GLOBALS['bors_data']['cache']['punbb_user'][$cur_post['poster_id']]);

	if(empty($cur_post['flag']))
	{
		include_once('funcs/users/geoip/get_flag.php');
		$cms_db->insert_ignore('posts_cached_fields', array(
			'post_id'	=> $cur_post['id'],
			'flag'		=> $cur_post['flag'] = "".get_flag($cur_post['poster_ip']),
			'create_time' => time(),
		));
	}
	
	$post_count++;
	$user_avatar = '';
	$user_info = array();
	$user_contacts = array();
	$post_actions = array();
	$signature = '';

	// If the poster is a registered user.
	if ($cur_post['poster_id'] > 1)
	{
		$username = pun_htmlspecialchars($cur_post['username']);

		$size = max(6, 12 - intval(strlen($username)/3));

		if(strlen($username) > 10)
		{
			$username = str_replace('_', '_ ', $username);
		
			$count = 0;
			for($i=0; $i<strlen($username); $i++)
			{
				if(!preg_match("!\s|\-!", $username{$i}))
					$count++;
				else
					$count = 0;
				
				if($count >= 12)
				{
					$username = substr($username, 0, $i).' '.substr($username, $i);
					$count = 0;
				}	
			}
		}
		
		$userlink = "<a href=\"{$pun_config['root_uri']}/profile.php?id={$cur_post['poster_id']}\" style=\"font-size: {$size}pt;\">".$username.'</a>';

		$user_title = get_title($poster);

		if ($pun_config['o_censoring'] == '1')
			$user_title = censor_words($user_title);

		if ($pun_config['o_avatars'] == '1' && $poster['use_avatar'] && ($pun_user['show_avatars'] != '0' || $pun_user['id'] <= 1))
			$user_avatar = "<img src=\"{$pun_config['root_uri']}/{$pun_config['o_avatars_dir']}/{$poster['use_avatar']}\" width=\"{$poster['avatar_width']}\" height=\"{$poster['avatar_height']}\" alt=\"\" />";
		else
			$user_avatar = '';

		// We only show location, register date, post count and the contact links if "Show user info" is enabled
		if ($pun_config['o_show_user_info'] == '1')
		{
			if ($poster['location'] != '')
			{
				if ($pun_config['o_censoring'] == '1')
					$poster['location'] = censor_words($poster['location']);

				$user_info[] = $lang_topic['From'].': '.pun_htmlspecialchars($poster['location']);
			}

			$user_info[] = $lang_common['Registered'].': '.date($pun_config['o_date_format'], $poster['registered']);

			if ($pun_config['o_show_post_count'] == '1' || $pun_user['g_id'] < PUN_GUEST)
				$user_info[] = $lang_common['Posts'].': '.$poster['num_posts'];

			// Now let's deal with the contact links (E-mail and URL)
			if (($poster['email_setting'] == '0' && !$pun_user['is_guest']) || $pun_user['g_id'] < PUN_GUEST)
				$user_contacts[] = "<a href=\"mailto:{$poster['email']}\">{$lang_common['E-mail']}</a>";
			else if ($poster['email_setting'] == '1' && !$pun_user['is_guest'])
				$user_contacts[] = "<a href=\"{$pun_config['root_uri']}/misc.php?email={$cur_post['poster_id']}\">{$lang_common['E-mail']}</a>";

			if ($poster['url'] != '' && $poster['url'] != 'http://')
				$user_contacts[] = '<a href="'.pun_htmlspecialchars($poster['url']).'">'.$lang_topic['Website'].'</a>';
		}

		if ($pun_user['g_id'] < PUN_GUEST)
		{
			$user_info[] = "IP: <a href=\"{$pun_config['root_uri']}/moderate.php?get_host={$cur_post['id']}\">".$cur_post['poster_ip'].'</a>';

			if ($poster['admin_note'] != '')
				$user_info[] = $lang_topic['Note'].': <strong>'.pun_htmlspecialchars($poster['admin_note']).'</strong>';
		}
		
		if($is_coordinator)
		{
			$user_info[] = "<a href=\"http://balancer.ru/user/{$cur_post['poster_id']}/warn_add/?ref=".urlencode("{$pun_config['root_uri']}/viewtopic.php?pid={$cur_post['id']}#p{$cur_post['id']}")."\" style=\"color: red;\">Выставить штраф</a>";
		}
		
		$user_info[] = "<a href=\"http://balancer.ru/user/{$cur_post['poster_id']}/reputation.html?post://{$cur_post['id']}\">Репутация участника</a>";
		$user_info[] = "<a href=\"http://balancer.ru/user/{$cur_post['poster_id']}/blog.html\">Блог участника</a>";
		$user_info[] = "<a href=\"http://balancer.ru/user/{$cur_post['poster_id']}/use-topics.html\">Темы с его участием</a>";
		$user_info[] = "<a href=\"http://balancer.ru/user/{$cur_post['poster_id']}/posts/\">Все сообщения участника</a>";

		if($is_coordinator)
		{
			$user_info[] = "<a href=\"http://balancer.ru/forum/tools/post/{$cur_post['id']}/\">Операции над сообщением</a>";
		}

	}
	// If the poster is a guest (or a user that has been deleted)
	else
	{
		$userlink = $username = pun_htmlspecialchars($cur_post['username']);
		$user_title = get_title($poster);

		if ($pun_user['g_id'] < PUN_GUEST)
			$user_info[] = "IP: <a href=\"{$pun_config['root_uri']}/moderate.php?get_host={$cur_post['id']}\">".$cur_post['poster_ip'].'</a>';

		if ($pun_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$pun_user['is_guest'])
			$user_contacts[] = '<a href="mailto:'.$cur_post['poster_email'].'">'.$lang_common['E-mail'].'</a>';
	}

	// Generation post action array (quote, edit, delete etc.)
	if (!$is_admmod)
	{
		if (!$pun_user['is_guest'])
			$post_actions[] = "<li class=\"postreport\"><a href=\"{$pun_config['root_uri']}/misc.php?report={$cur_post['id']}\">".$lang_topic['Report'].'</a>';

		if ($cur_topic['closed'] == '0')
		{
			if ($cur_post['poster_id'] == $pun_user['id'])
			{
				if ((($start_from + $post_count) == 1 && $pun_user['g_delete_topics'] == '1') || (($start_from + $post_count) > 1 && $pun_user['g_delete_posts'] == '1'))
					$post_actions[] = "<li class=\"postdelete\"><a href=\"{$pun_config['root_uri']}/delete.php?id={$cur_post['id']}\">".$lang_topic['Delete'].'</a>';
				if ($pun_user['g_edit_posts'] == '1')
					$post_actions[] = "<li class=\"postedit\"><a href=\"{$pun_config['root_uri']}/edit.php?id={$cur_post['id']}\">".$lang_topic['Edit'].'</a>';
			}

			if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1')
				$post_actions[] = "<li class=\"postquote\"><a href=\"{$pun_config['root_uri']}/post.php?tid=$id&amp;qid={$cur_post['id']}\">".$lang_topic['Quote'].'</a>';
		}
	}
	else
		$post_actions[] = "<li class=\"postreport\"><a href=\"{$pun_config['root_uri']}/misc.php?report={$cur_post['id']}\">".$lang_topic['Report'].'</a>'.$lang_topic['Link separator']."</li><li class=\"postdelete\"><a href=\"{$pun_config['root_uri']}/delete.php?id={$cur_post['id']}\">".$lang_topic['Delete'].'</a>'.$lang_topic['Link separator']."</li><li class=\"postedit\"><a href=\"{$pun_config['root_uri']}/edit.php?id={$cur_post['id']}\">".$lang_topic['Edit'].'</a>'.$lang_topic['Link separator']."</li><li class=\"postquote\"><a href=\"{$pun_config['root_uri']}/post.php?tid=$id&amp;qid={$cur_post['id']}\">".$lang_topic['Quote'].'</a>';


	// Switch the background color for every message.
	$bg_switch = ($bg_switch) ? $bg_switch = false : $bg_switch = true;
	$vtbg = ($bg_switch) ? ' roweven' : ' rowodd';

	$x = $cms_db->get('SELECT message, html FROM messages WHERE id='.$cur_post['id']);

	$cur_post['message'] = $x['message'];
	$cur_post['html'] = $x['html'];

	// Perform the main parsing of the message (BBCode, smilies, censor words etc)
	if(empty($cur_post['html']))
	{
		$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);
		$cms_db->update('messages', "id = {$cur_post['id']}", array('html' => $cur_post['message']));
	}
	else
		$cur_post['message'] = $cur_post['html'];

	// Do signature parsing/caching
	if ($poster['signature'] != '' && $pun_user['show_sig'] != '0')
	{
		if(empty($poster['signature_html']))
		{

			$signature = lcml($poster['signature'], 
					array(
						'cr_type' => 'save_cr',
						'forum_type' => 'punbb',
						'forum_base_uri' => 'http://balancer.ru/forum',
						'sharp_not_comment' => true,
						'html_disable' => true,
				));

			$cms_db->update('users', "id = {$poster['id']}", array('signature_html' => $signature));
		}
		else
			$signature = $poster['signature_html'];
	}

	// Attachment Mod Block Start
	$attach_allow_download = false;
	$attach_output = '';
	$attach_num = 0;
	// Check if this post has any attachments
	$result_attach = $db->query("
		SELECT 
			af.id, 
			af.filename, 
			af.size, 
			af.downloads, 
			af.location  
		FROM {$db->prefix}attach_2_files AS af 
		WHERE af.post_id=".intval($cur_post['id']))
			or error('Unable to fetch if there were any attachments to the post', __FILE__, __LINE__, $db->error());

	include_once('inc/attaches.php');

	$attach_num = $db->num_rows($result_attach);
	if($attach_num > 0)
	{
		$attach_allow_download=true;

		if($attach_allow_download)
		{	//check if the user is allowed to download it.
			$attach_output .= $lang_attach['Attachments:'].' ';
			while(list($attachment_id, $attachment_filename, $attachment_size, $attachment_downloads, $location)=$db->fetch_row($result_attach))
			{
				$attachment_extension=attach_get_extension($attachment_filename);
				$attach_output .= "<div class=\"codebox\">".attach_icon($attachment_extension)." <a href=\"{$pun_config['root_uri']}/attachment.php?item=$attachment_id\">$attachment_filename</a>, {$lang_attach['Size:']} ".number_format($attachment_size).' '.$lang_attach['bytes'].', '.$lang_attach['Downloads:'].' '.number_format($attachment_downloads);
				if(preg_match("!(jpe?g|png|gif)!i", $attachment_extension))
					$attach_output .= "<br /><a href=\"{$pun_config['root_uri']}/attachment.php?item=$attachment_id\"><img src=\"http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/468x468/$1", $location)."\"></a>";
				$attach_output .= "</div>";
			}
		}
	}
	// Attachment Mod Block End

	$user_warn_count	= intval($poster['warnings']);
	$user_warn = $user_warn_count ? "<a href=\"http://balancer.ru/user/{$cur_post['poster_id']}/warnings/\"><img src=\"http://balancer.ru/user/{$cur_post['poster_id']}/warnings.gif\" width=\"100\" height=\"16\" border=\"0\"></a>\n" : "";
//	$user_warn = $user_warn_count ? "<script src=\"http://balancer.ru/user/{$cur_post['poster_id']}/warnings.js\" type=\"text/javascript\"></script>\n" : "";
?>
<div id="p<?php echo $cur_post['id'] ?>" class="blockpost<?php echo $vtbg ?><?php if (($post_count + $start_from) == 1) echo ' firstpost'; ?>">
	<h2><b>
		<span onClick="setImageId('post_<?echo $cur_post['id'];?>_moreimg', toggleVisId('post_<?echo $cur_post['id'];?>_more') == 1, 'http://balancer.ru/cms/templates/forum/icons/16x16/actions/down.gif', 'http://balancer.ru/cms/templates/forum/icons/16x16/actions/next.gif')">
			<img id="post_<?echo $cur_post['id'];?>_moreimg" src="http://balancer.ru/cms/templates/forum/icons/16x16/actions/next.gif" alt="*" />
			<?echo $username;?></span></b>,
	<?
		echo $cur_post['flag'];
	?> <a href="<? echo $pun_config['root_uri'];?>/viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']); /*"*/ ?>
	<span class="conr">#<?php echo ($start_from + $post_count) ?>&nbsp;</span></a><?
	if($cur_post['answer_to'])
		echo "; Ответ на <a href=\"{$pun_config['root_uri']}/viewtopic.php?pid={$cur_post['answer_to']}#p{$cur_post['answer_to']}\">{$cur_post['up_poster']} (". format_time($cur_post['up_posted']) . ")</a>";
?>
	</h2>
	<div class="box">
		<div class="inbox">
			<div class="postright">
				<div class="postmsg">
					<div class="userinfo" style="width: 100px; float: right; margin: 0px 0px 8px 16px; display: inline; border: 1px solid; background-color: white;">
						<div align="center">
							<? if($user_avatar) echo "<div style=\"width: 100px; height: 100px; text-align: center; vertical-align: middle; overflow: hidden;\" onClick=\"setImageId('post_{$cur_post['id']}_moreimg', toggleVisId('post_{$cur_post['id']}_more') == 1, 'http://balancer.ru/cms/templates/forum/icons/16x16/actions/down.gif', 'http://balancer.ru/cms/templates/forum/icons/16x16/actions/next.gif')\">$user_avatar</div>";?>
							<div style="font-size: x-small; font-weight: 900;"><?echo $userlink;?></div>
							<div style="font-size: xx-small;"><?echo$user_title;?></div>
							<center>
							<?
								if($poster['reputation'])
									echo "<a href=\"http://balancer.ru/user/{$cur_post['poster_id']}/reputation.html?post://{$cur_post['id']}\"><img src=\"http://balancer.ru/user/{$cur_post['poster_id']}/rep.gif\" width=\"100\" height=\"16\" border=\"0\" alt=\"*\" /></a>\n";
							?>
							<div><?echo $user_warn;?></div>
							</center>
						</div>
					</div>
					<div id="post_<?echo $cur_post['id'];?>_more" style="margin: 0; padding: 0; display: none; font-size: 80%;">
<ul>
<? if (count($user_info))
	foreach($user_info as $ui)
		echo "<li>$ui</li>\n";
?>
<? if (count($user_contacts))
	foreach($user_contacts as $ui)
		echo "<li>$ui</li>\n";
?>
</ul>
<div style="border-bottom-style: dashed; border-bottom-width: 1px; margin-bottom: 8px;"></div>
					</div>
					<?php echo $cur_post['message']."\n" ?>
<?php if ($cur_post['edited'] != '') echo "\t\t\t\t\t".'<p class="postedit"><em>'.$lang_topic['Last edit'].' '.pun_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'."\n"; ?>
<?php if ($attach_allow_download) echo "\t\t\t\t\t".'<div class="postsignature" style="text-align: left;"><hr />'.$attach_output.'</div>'."\n"; ## Attachment Mod row ?>
				</div>
<?php if ($signature != '') echo "\t\t\t\t".'<div class="postsignature"><hr />'.$signature.'</div>'."\n"; ?>
			<div class="clearer"></div>
			<div class="postfootright"><?php echo (count($post_actions)) ? '<ul>'.implode($lang_topic['Link separator'].'</li>', $post_actions).'</li></ul></div>'."\n" : '<div>&nbsp;</div></div>'."\n" ?>
			</div>
		</div>
	</div>
</div>

<?php

}

?>
		<a name="page_bottom"></a>

		</td>
		<td class="topic_right_column" valign="top" style="border-style: none;">
<?include("design/right.php");?>
		</td>
	</tr>
</table>

<div class="postlinksb">
	<div class="inbox">
		<ul><li>
				<span class="pagelink conl">
					<a href="#page_top"><img src="http://balancer.ru/cms/templates/forum/icons/16x16/actions/up.gif" alt="#top" border="0"/></a>
					<?php echo $paging_links ?>
				</span>
				<span class="postlink conr"><?php echo $post_link ?></span>
		</li></ul>
		<br/>
		<ul><li><a href="<? echo $pun_config['root_uri'];?>/index.php"><?php echo $lang_common['Index'] ?></a></li><li>&nbsp;&raquo;&nbsp;<a href="<? echo $pun_config['root_uri'];?>/viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><?php /*"*/ echo pun_htmlspecialchars($cur_topic['forum_name']) ?></a></li><li>&nbsp;&raquo;&nbsp;<?php echo pun_htmlspecialchars($cur_topic['subject']) ?></li></ul>
		<?php echo $subscraction ?>
	</div>
</div>

<?php

// Display quick post if enabled
if ($quickpost)
{

?>
<div class="blockform">
	<h2><span><?php echo $lang_topic['Quick post'] ?></span></h2>
	<div class="box">
		<form method="post" action="post.php?tid=<?php echo $id;/*"*/?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Write message legend'] ?></legend>
					<div class="infldset txtarea">
						<input type="hidden" name="form_sent" value="1" />
						<label><textarea name="req_message" rows="7" cols="75" tabindex="1"></textarea></label>
						<ul class="bblinks">
							<li><a href="<? echo $pun_config['root_uri'];?>/help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang_common['BBCode'] ?></a>: <?php echo ($pun_config['p_message_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="<? echo $pun_config['root_uri'];?>/help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang_common['img tag'] ?></a>: <?php echo ($pun_config['p_message_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="<? echo $pun_config['root_uri'];?>/help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang_common['Smilies'] ?></a>: <?php echo ($pun_config['o_smilies'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
						</ul>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="submit" tabindex="2" value="<?php echo $lang_common['Submit'];/*"*/?>" accesskey="s" /></p>
		</form>
	</div>
</div>
<?php

}


$forum_id = $cur_topic['forum_id'];
$footer_style = 'viewtopic';
require 'footer.php';
