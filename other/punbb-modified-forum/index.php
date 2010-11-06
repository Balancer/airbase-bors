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

$fdiff = 0;
$tdiff = 0;
$pdiff = 0;

if($_SERVER['HTTP_HOST']=='la2.wrk.ru' || $_SERVER['HTTP_HOST']=='la2.balancer.ru')
{
	$fdiff = 105;
	$tdiff = 41000;
	$pdiff = 794000;

	if(preg_match("!^/forum/index\.php/board,(\d+)\.(\d+)\.html$!", $_SERVER['REQUEST_URI'], $m))
	{
		define('BORS_CORE', '/var/www/.bors/bors-core');
		define('BORS_LOCAL', '/var/www/.bors/bors-airbase');
		define('BORS_HOST', $_SERVER['DOCUMENT_ROOT'].'/cms-local');
	
		require_once(BORS_CORE.'/init.php');

		$forum_id = $_GET['id'] = $m[1]+$fdiff;
		$page = $_GET['p'] = $m[2];
		go("/forum/viewforum.php?id={$forum_id}&p={$page}", true);
		exit();
	}

}

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';

if(
	preg_match('/pid=(\d+)/', @$_SERVER['QUERY_STRING'], $m)
		|| preg_match('/pid=(\d+)?pid=\d+/', @$_SERVER['QUERY_STRING'], $m)
)
{
	if($post = object_load('forum_post', $m[1] + $pdiff))
		return go($post->url_in_topic());
	debug_hidden_log('__trap', "Unknown post in ".$_SERVER['QUERY_STRING']);
	return go('/');
}

if(!empty($_GET['topic']) && preg_match("!^(\d+)\.msg(\d+)$!", $_GET['topic'], $m))
{
	//http://forums.airbase.ru/index.php?topic=27581.msg415049
	go("http://balancer.ru/forum/punbb/viewtopic.php?pid={$m[2]}#p{$m[2]}");
}

if(!empty($_GET['topic']) && (
	preg_match("!^(\d+)\.msg!", $_GET['topic'], $m)
	|| preg_match("!^(\d+)\.0!", $_GET['topic'], $m)
))
{
	//http://forums.airbase.ru/index.php?topic=27581.msg415049
	go("http://balancer.ru/forum/punbb/viewtopic.php?id={$m[1]}");
}

if(!empty($_SERVER['REQUEST_URI']) && preg_match("!topic,(\d+).(\d+)\.html$!", $_SERVER['REQUEST_URI'], $m))
{
	//http://forums.airbase.ru/index.php/topic,2760.0.html
	if(empty($m[2]))
		go("http://balancer.ru/forum/punbb/viewtopic.php?id={$m[1]}");
	else
		go("http://balancer.ru/forum/punbb/viewtopic.php?id={$m[1]}&p=".(intval(($m[2]-1)/25)+1));
}

if(!empty($_GET['showforum']))
{
	//http://forums.airbase.ru/index.php?showforum=40
	go("http://forums.airbase.ru/viewforum.php?id={$_GET['showforum']}");
}

function punbb_index_bors_load()
{
	require_once('include/bors_config.php');
}


if(!empty($_GET['view']))
{
	//http://forums.airbase.ru/?showtopic=3938&view=findpost&p=362293
	if($_GET['view'] == 'findpost' && !empty($_GET['p']))
	{
		punbb_index_bors_load();
		$obj = object_load('forum_post', intval($_GET['p']));
		return go($obj ? $obj->url() : '/');
	}
}

if(!empty($_GET['pid']))
{
	$_GET['pid'] += $tdiff;
	include("viewtopic.php");
	exit();
}

if(!empty($_GET['act']))
{
	switch($_GET['act'])
	{
		case 'SF':
			punbb_index_bors_load();
			$obj = object_load('forum_forum', intval($_GET['f']));
			return go($obj->url());
	}
	
	//http://forums.airbase.ru/index.php?act=Print&client=printer&f=61&t=25524
	if(!empty($_GET['t']))
	{
		$_GET['id'] = $_GET['t'];
		include("viewtopic.php");
		exit();
	}

}

if(!empty($_GET['showtopic']))
{
	punbb_index_bors_load();
	$obj = object_load('forum_topic_ipbst', $_GET['showtopic'] + $tdiff, array('page' => intval($_GET['st'])));
	return go($obj->url());
}



//print_r($_GET); exit();

if(@$_GET['action'] == 'recent')
{
	$_GET['action'] = 'show_24h';
	include("search.php");
	exit();
}


if(@$_GET['action'] == 'unreadreplies')
{
	$_GET['action'] = 'show_new';
	include("search.php");
	exit();
}


if($_SERVER['HTTP_HOST'] != 'balancer.ru' || !preg_match("!^/forum!", $_SERVER['REQUEST_URI']))
{
	include(PUN_ROOT.'viewcat.php');
	exit();
}

//print_r($pun_config);

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);


// Load the index.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/index.php';


$page_title = pun_htmlspecialchars($pun_config['o_board_title']);
define('PUN_ALLOW_INDEX', 1);
require PUN_ROOT.'header.php';

forum_forum::all_forums_preload(true);

?>
<ul><li><b>
<?
	$self = object_load('http://balancer.ru/forum/');
	$nav = object_load('module_nav_top', $self);
	echo $nav->body();
?>
</li></b></ul>
<?

include_once("include/subforums.php");
$ich = new bors_cache();
if($ich->get("subforums-text-v4", $pun_config['root_uri']))// && !debug_is_balancer()
{
	$subforums = $ich->last();
}
else
{
//	if(debug_is_balancer())
//		echo config('cache_engine').', cd='.config('cache_disabled');

/*	if(debug_is_balancer())
	{
		foreach($cms_db->get_array("SELECT id FROM forums") as $iid)
//			if(!array_key_exists($iid, $subforums))
				$subforums[$iid] = subforums_text(object_load('forum_forum', $iid));
	}
	else */
//	{
		foreach($cms_db->get_array("SELECT id FROM forums") as $iid)
			$subforums[$iid] = get_subforums_text(punbb_get_all_subforums($iid));
//	}

	$ich->set($subforums, -7200);
}

$db = new driver_mysql('punbb');

// Print the categories and forums
$result = $db->query("SELECT c.id AS cid, 
		c.cat_name, 
		c.base_uri as cat_base_uri,
		f.id AS fid, 
		f.forum_name, 
		f.forum_desc, 
		f.redirect_url, 
		f.moderators, 
		f.num_topics, 
		f.num_posts, 
		f.last_post, 
		f.last_post_id, 
		f.last_poster 
	FROM {$db->prefix}categories AS c 
		LEFT JOIN {$db->prefix}forums AS f ON c.id=f.cat_id 
		LEFT JOIN {$db->prefix}forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id={$pun_user['g_id']}) 
	WHERE f.parent IS NULL
		AND (fp.read_forum IS NULL OR fp.read_forum=1) 
		AND c.skip_common = 0
	ORDER BY c.disp_position, c.id, f.disp_position", true) 
	or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

$cur_category = 0;
$cat_count = 0;
while ($cur_forum = $db->fetch($result))
{
	if($cur_forum['fid'])
		$forum = object_load('forum_forum', intval($cur_forum['fid']));

	$moderators = '';

	if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
	{
		if ($cur_category != 0)
			echo "\t\t\t".'</tbody>'."\n\t\t\t".'</table>'."\n\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n\n";

		++$cat_count;

?>
<div id="idx<?php echo $cat_count;/*"*/?>" class="blocktable">
	<h2><span><a href="<?echo pun_htmlspecialchars($cur_forum['cat_base_uri'])?>"><?php echo pun_htmlspecialchars($cur_forum['cat_name']) ?></a></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Forum'] ?></th>
					<th class="tc2" scope="col"><?php echo $lang_index['Topics'] ?></th>
					<th class="tc3" scope="col"><?php echo $lang_common['Posts'] ?></th>
					<th class="tcr" scope="col"><?php echo $lang_common['Last post'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php

		$cur_category = $cur_forum['cid'];
	}

	$item_status = '';
	$icon_text = $lang_common['Normal icon'];
	$icon_type = 'icon';

	// Are there new posts?
	if (!$pun_user['is_guest'] && $cur_forum['last_post'] > $pun_user['last_visit'])
	{
		$item_status = 'inew';
		$icon_text = $lang_common['New icon'];
		$icon_type = 'icon inew';
	}

	// Is this a redirect forum?
	if ($cur_forum['redirect_url'] != '')
	{
		$forum_field = '<h3><a href="'.pun_htmlspecialchars($cur_forum['redirect_url']).'" title="'.$lang_index['Link to'].' '.pun_htmlspecialchars($cur_forum['redirect_url']).'">'.pun_htmlspecialchars($cur_forum['forum_name']).'</a></h3>';
		$num_topics = $num_posts = '&nbsp;';
		$item_status = 'iredirect';
		$icon_text = $lang_common['Redirect icon'];
		$icon_type = 'icon';
	}
	else
	{
		$forum_field = "<h3><a href=\"{$pun_config['root_uri']}/viewforum.php?id={$cur_forum['fid']}\">".pun_htmlspecialchars($cur_forum['forum_name']).'</a></h3>';
		$num_topics = $cur_forum['num_topics'];
		$num_posts = $cur_forum['num_posts'];
	}

	if ($cur_forum['forum_desc'] != '')
		$forum_field .= "\n\t\t\t\t\t\t\t\t".$cur_forum['forum_desc'];

	if(0 && debug_is_balancer())
	{
		if($subs = $forum->all_readable_subforum_ids())
			$forum_field .= subforums_text($subs);
	}
	else
	{
		if($subforums[$cur_forum['fid']])
			$forum_field .= $subforums[$cur_forum['fid']];
	}

	// If there is a last_post/last_poster.
	if ($cur_forum['last_post'] != '')
		$last_post = "<a href=\"{$pun_config['root_uri']}/viewtopic.php?pid={$cur_forum['last_post_id']}#p{$cur_forum['last_post_id']}\">".format_time($cur_forum['last_post']).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($cur_forum['last_poster']).'</span>';
	else
		$last_post = '&nbsp;';

	if ($cur_forum['moderators'] != '')
	{
		$mods_array = unserialize($cur_forum['moderators']);
		$moderators = array();

		while (list($mod_username, $mod_id) = @each($mods_array))
			$moderators[] = "<a href=\"{$pun_config['root_uri']}/profile.php?id=$mod_id\">".pun_htmlspecialchars($mod_username).'</a>';

		$moderators = "\t\t\t\t\t\t\t\t".'<p><em>('.$lang_common['Moderated by'].'</em> '.implode(', ', $moderators).')</p>'."\n";
	}

?>
 				<tr<?php if ($item_status != '') echo ' class="'.$item_status.'"'; ?>>
					<td class="tcl">
						<div class="intd">
							<div class="<?php echo $icon_type;/*"*/?>"><div class="nosize"><?php echo $icon_text ?></div></div>
							<div class="tclcon">
								<?php echo $forum_field."\n".$moderators ?>
							</div>
						</div>
					</td>
					<td class="tc2"><?php echo $num_topics ?></td>
					<td class="tc3"><?php echo $num_posts ?></td>
					<td class="tcr"><?php echo $last_post ?></td>
				</tr>
<?php

} // end while?

// Did we output any categories and forums?
if ($cur_category > 0)
	echo "\t\t\t".'</tbody>'."\n\t\t\t".'</table>'."\n\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n\n";
else
	echo '<div id="idx0" class="block"><div class="box"><div class="inbox"><p>'.$lang_index['Empty board'].'</p></div></div></div>';

// Collect some statistics from the database
$stats['total_users'] = $cms_db->select($db->prefix.'users', 'COUNT(*)', array());
$stats['last_user'] = $cms_db->select($db->prefix.'users', 'id, username', array('order' => '-registered', 'limit' => 1));

//list($stats['total_topics'], $stats['total_posts']) = array_values($cms_db->select($db->prefix.'forums', 'SUM(num_topics), SUM(num_posts)', array()));
$stats['total_topics'] = $cms_db->select($db->prefix.'topics', 'COUNT(*)', array());
$stats['total_posts']  = $cms_db->select($db->prefix.'posts',  'COUNT(*)', array());

?>
<div id="brdstats" class="block">
	<h2><span><?php echo $lang_index['Board info'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<dl class="conr">
				<dt><strong><?php echo $lang_index['Board stats'] ?></strong></dt>
				<dd><?php echo $lang_index['No of users'].': <strong>'. $stats['total_users'] ?></strong></dd>
				<dd><?php echo $lang_index['No of topics'].': <strong>'.$stats['total_topics'] ?></strong></dd>
				<dd><?php echo $lang_index['No of posts'].': <strong>'.$stats['total_posts'] ?></strong></dd>
			</dl>
			<dl class="conl">
				<dt><strong><?php echo $lang_index['User info'] ?></strong></dt>
				<dd><?php echo $lang_index['Newest user'] ?>: <a href="<?echo $pun_config['root_uri']?>/profile.php?id=<?php echo $stats['last_user']['id'];/*"*/?>"><?php echo pun_htmlspecialchars($stats['last_user']['username']) ?></a></dd>
<?php

if ($pun_config['o_users_online'] == '1')
{
	echo "<script  type=\"text/javascript\" src=\"http://balancer.ru/js/stat-users.js\"></script>\n";
	echo "<script  type=\"text/javascript\" src=\"http://balancer.ru/js/stat-os.js\"></script>\n";
	echo "<script  type=\"text/javascript\" src=\"http://balancer.ru/js/stat-browsers.js\"></script>\n";

}
else
	echo "\t\t".'</dl>'."\n\t\t\t".'<div class="clearer"></div>'."\n";


?>
		</div>
	</div>
</div>
<?php

$footer_style = 'index';
require PUN_ROOT.'footer.php';
