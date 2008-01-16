<?php

define('PUN_ROOT', './');

include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
include_once("funcs/Cache.php");

require PUN_ROOT.'include/common.php';

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);

// Load the index.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/index.php';

$page_title = pun_htmlspecialchars($pun_config['o_board_title']);
define('PUN_ALLOW_INDEX', 1);
require PUN_ROOT.'header.php';

include_once("include/subforums.php");
$ich = new Cache();
if($ich->get("subforums-text-v4", $pun_config['root_uri']))
	$subforums = $ich->last();
else
{
	foreach($cms_db->get_array("SELECT id FROM forums") as $iid)
		$subforums[$iid] = get_subforums_text(punbb_get_all_subforums($iid));
	$ich->set($subforums, -600);
}

if($ich->get("cat_names-v3", "all"))
	$cat_names = $ich->last();
else
{
	foreach($cms_db->get_array("SELECT id, cat_name, base_uri FROM categories") as $cat)
		$cat_names[$cat['id']] = $cat['cat_name'];
	$ich->set($cat_names, -600);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if(!$id)
{
//	$uri = "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
//	echo $GLOBALS['main_uri']."<br />";
	foreach($cms_db->get_array("SELECT * FROM categories ORDER BY parent, disp_position") as $r)
	{
//		echo "Check: {$r['base_uri']}<br />\n";
		if($r['base_uri'] && preg_match("!^{$r['base_uri']}!", $GLOBALS['main_uri']))
		{
			$id = intval($r['id']);
			break;
		}
	}
}

include_once("tools/inc.php");
$ids = punbb_get_all_subcategories($id);
$ids[] = $id;
$ids = join(",", $ids);

if($ids)
	foreach($cms_db->get_array("SELECT forum_id, last_visit FROM forum_visits LEFT JOIN forums ON forum_id = id WHERE cat_id IN ($ids) AND user_id=".intval($pun_user['id'])) as $row)
		$visits[$row['forum_id']] = $row['last_visit'];
else
	foreach($cms_db->get_array("SELECT forum_id, last_visit FROM forum_visits WHERE user_id=".intval($pun_user['id'])) as $row)
		$visits[$row['forum_id']] = $row['last_visit'];

if(empty($pun_user['g_id']))
	$pun_user['g_id'] = 3;

// Print the categories and forums
$result = $db->query("
		SELECT 
			c.id AS cid, 
			c.cat_name, 
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
		WHERE f.parent = 0
			AND f.cat_id IN($ids)
			AND (fp.read_forum IS NULL OR fp.read_forum=1) 
		ORDER BY c.disp_position, c.id, f.disp_position", true) 
	or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

$cur_category = 0;
$cat_count = 0;
while ($cur_forum = $db->fetch_assoc($result))
{
	$moderators = '';

	if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
	{
		if ($cur_category != 0)
			echo "\t\t\t".'</tbody>'."\n\t\t\t".'</table>'."\n\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n\n";

		++$cat_count;

		if($cur_forum['cat_parent'] != 0)
			$cur_forum['cat_name'] = $cat_names[$cur_forum['cat_parent']]." : ".$cur_forum['cat_name'];

?>
<div id="idx<?echo $cat_count;/*"*/?>" class="blocktable">
	<h2><span><?php echo pun_htmlspecialchars($cur_forum['cat_name']) ?></span></h2>
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
	if (!$pun_user['is_guest'] 
		&& $cur_forum['last_post'] > @$visits[$cur_forum['fid']]
		&& (@$visits[$cur_forum['fid']] > 0 || $cur_forum['last_post'] > $pun_user['last_visit'])
		)
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

	if($subforums[$cur_forum['fid']])
		$forum_field .= $subforums[$cur_forum['fid']];

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
							<div class="<?php echo $icon_type;/*"*/ ?>"><div class="nosize"><?php echo $icon_text ?></div></div>
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

}

// Did we output any categories and forums?
if ($cur_category > 0)
	echo "\t\t\t".'</tbody>'."\n\t\t\t".'</table>'."\n\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n\n";
else
	echo '<div id="idx0" class="block"><div class="box"><div class="inbox"><p>'.$lang_index['Empty board'].'</p></div></div></div>';


// Collect some statistics from the database
$result = $db->query('SELECT COUNT(id)-1 FROM '.$db->prefix.'users') or error('Unable to fetch total user count', __FILE__, __LINE__, $db->error());
$stats['total_users'] = $db->result($result);

$result = $db->query('SELECT id, username FROM '.$db->prefix.'users ORDER BY registered DESC LIMIT 1') or error('Unable to fetch newest registered user', __FILE__, __LINE__, $db->error());
$stats['last_user'] = $db->fetch_assoc($result);

$result = $db->query('SELECT SUM(num_topics), SUM(num_posts) FROM '.$db->prefix.'forums') or error('Unable to fetch topic/post count', __FILE__, __LINE__, $db->error());
list($stats['total_topics'], $stats['total_posts']) = $db->fetch_row($result);

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
				<dd><?php echo $lang_index['Newest user'] ?>: <a href="<? echo $pun_config['root_uri'];?>/profile.php?id=<?php echo $stats['last_user']['id'];/*"*/?>"><?php echo pun_htmlspecialchars($stats['last_user']['username']) ?></a></dd>
<?php

if ($pun_config['o_users_online'] == '1')
{
	// Fetch users online info and generate strings for output
	$num_guests = 0;
	$users = array();
	$result = $db->query('SELECT user_id, ident FROM '.$db->prefix.'online WHERE idle=0 ORDER BY ident', true) or error('Unable to fetch online list', __FILE__, __LINE__, $db->error());

	while ($pun_user_online = $db->fetch_assoc($result))
	{
		if ($pun_user_online['user_id'] > 1)
			$users[] = "\n\t\t\t\t<dd><a href=\"{$pun_config['root_uri']}/profile.php?id={$pun_user_online['user_id']}\">".pun_htmlspecialchars($pun_user_online['ident']).'</a>';
		else
			++$num_guests;
	}

	$num_users = count($users);
	echo "\t\t\t\t".'<dd>'. $lang_index['Users online'].': <strong>'.$num_users.'</strong></dd>'."\n\t\t\t\t".'<dd>'.$lang_index['Guests online'].': <strong>'.$num_guests.'</strong></dd>'."\n\t\t\t".'</dl>'."\n";


	if ($num_users > 0)
		echo "\t\t\t".'<dl id="onlinelist" class= "clearb">'."\n\t\t\t\t".'<dt><strong>'.$lang_index['Online'].':&nbsp;</strong></dt>'."\t\t\t\t".implode(',</dd> ', $users).'</dd>'."\n\t\t\t".'</dl>'."\n";
	else
		echo "\t\t\t".'<div class="clearer"></div>'."\n";

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
