<?
	include_once(PUN_ROOT.'tools/inc.php');

	function get_subforums_html($forum)
	{
		$forum = intval($forum);
	
		global $pun_user, $pun_config;
		
		$db = new DataBase('punbb');

		if($db->get("SELECT COUNT(*) FROM forums WHERE parent = $forum") == 0)
			return "";
?>
<div class="blocktable">
	<h2><span>Подфорумы</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col">Форум</th>
					<th class="tc2" scope="col">Тем</th>
					<th class="tc3" scope="col">Сообщений</th>
					<th class="tcr" scope="col">Последнее сообщение</th>
				</tr>
			</thead>
			<tbody>
<?	
		foreach($db->get_array("
			SELECT 
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
			FROM {$db->prefix}forums AS f
				LEFT JOIN {$db->prefix}forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id={$pun_user['g_id']}) 
			WHERE f.parent = $forum
				AND (fp.read_forum IS NULL OR fp.read_forum=1) 
			ORDER BY f.disp_position") as $cur_forum)
		{
			$fid = $cur_forum['fid'];

		
			$moderators = '';

			$item_status = '';
			$icon_text = $lang_common['Normal icon'];
			$icon_type = 'icon';

			// Are there new posts?
			if(!$pun_user['is_guest'])
			{
				$last_visit = $db->get("SELECT last_visit FROM forum_visits WHERE forum_id = $fid AND user_id=".intval($pun_user['id']));
				if($cur_forum['last_post'] > $last_visit
					&& ($last_visit > 0 || $cur_forum['last_post'] > $pun_user['last_visit'])
				)
				{
					$item_status = 'inew';
					$icon_text = $lang_common['New icon'];
					$icon_type = 'icon inew';
				}
			}

			// Is this a redirect forum?
			if ($cur_forum['redirect_url'] != '')
			{
				$forum_field = "<h3><a href=\"".pun_htmlspecialchars($cur_forum['redirect_url'])."\" title=\"{$lang_index['Link to']} ".pun_htmlspecialchars($cur_forum['redirect_url']).'">'.pun_htmlspecialchars($cur_forum['forum_name'])."</a></h3>\n";
				$num_topics = $num_posts = '&nbsp;';
				$item_status = 'iredirect';
				$icon_text = $lang_common['Redirect icon'];
				$icon_type = 'icon';
			}
			else
			{
				$forum_field = "<h3><a href=\"{$pun_config['root_uri']}/viewforum.php?id={$cur_forum['fid']}\">".pun_htmlspecialchars($cur_forum['forum_name'])."</a></h3>\n";
				$num_topics = $cur_forum['num_topics'];
				$num_posts = $cur_forum['num_posts'];
			}

			if($cur_forum['forum_desc'] != '')
				$forum_field .= $cur_forum['forum_desc']."\n";

			$subforums = punbb_get_all_subforums($fid);
			if($subforums)
				$forum_field .= get_subforums_text($subforums);

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
<?
		}
?>
			</tbody>
			</table>
		</div>
	</div>
</div>
<?
	}

function subforums_text($forum)
{
	$subs = array();
	foreach($forum->all_readable_subforum_ids() as $subfid)
	{
		$f = object_load('forum_forum', $subfid);
		$subs[] = $f->titled_url();
	}

	return "<div class=\"subforums\"><b>Подфорумы:</b> ".join(", ", $subs)."</div>\n";
}

function get_subforums_text($forums)
{
		if(empty($forums))
			return "";
	
		global $pun_user, $pun_config, $lang_index;
		
		$db = new DataBase('punbb');

		$subforums = array();
		foreach($forums as $fid)
		{
			$cur_forum = $db->get("
				SELECT 
					id AS fid, 
					forum_name, 
					redirect_url, 
					moderators, 
					num_topics, 
					num_posts, 
					last_post, 
					last_post_id, 
					last_poster 
				FROM forums WHERE id = $fid");
		
			// Is this a redirect forum?
			if($cur_forum['redirect_url'] != '')
				$subforums[] = "<a href=\"".htmlspecialchars($cur_forum['redirect_url'])."\" title=\"{$lang_index['Link to']} ".htmlspecialchars($cur_forum['redirect_url']).'">'.htmlspecialchars($cur_forum['forum_name'])."</a>";
			else
				$subforums[] = "<a href=\"{$pun_config['root_uri']}/viewforum.php?id=$fid\">".htmlspecialchars($cur_forum['forum_name'])."</a>";
		}

		$db->close();		
		return "<div class=\"subforums\"><b>Подфорумы:</b> ".join(", ", $subforums)."</div>\n";
}
