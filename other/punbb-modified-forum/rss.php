<?php
/**
* Punn BB creates an RSS feed for New Posts since last visit
* half written half hacked by Chris Neal
*/

// set header
header("Content-Type: text/xml");
// call Punn BB constants
define('PUN_ROOT', './');
define('PUN_QUIET_VISIT', 1);
require PUN_ROOT.'include/common.php';

//set configuation options
$title = pun_htmlspecialchars($pun_config['o_board_title']);
$desc = $title." Rss Feed";
$baseURL = $pun_config['o_base_url'];

//find out if user has a cookie
if (isset($_COOKIE['fa_cookie']))
    list($cookie['user_id'], $cookie['password_hash']) = @unserialize($_COOKIE['fa_cookie']);
if ($cookie['user_id'] > 1)
{
	$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>'.$pun_user['last_visit']) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
} else{
	$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>'.(time() - 3600*6)) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
}
//run query functions
$num_hits = $db->num_rows($result);
if (!$num_hits){
echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
echo '	<rss version="2.0">'."\n";
echo '		<channel>'."\n";
echo '			<title>'.$title .'</title>'."\n";
echo '			<link>'.$baseURL.'</link>'."\n";
echo '			<description>'.$desc .' '.count($search_set).'</description>'."\n";
echo '			<language>en</language>'."\n";
echo '			<copyright>© '.$title .'</copyright>'."\n";
echo '			<lastBuildDate>'.date("r").'</lastBuildDate>'."\n";
echo '			<item>'."\n"; 
echo '			<title>No Recent Posts</title> '."\n";
echo '			<link>'.$baseURL.'/</link>'."\n";
echo '			<description>No new Posts</description> '."\n";
echo'			</item>'."\n";
echo'		</channel>'."\n";
echo'	</rss>';
} else {
$sort_by = 4;
$search_ids = array();
while ($row = $db->fetch_row($result))
	$search_ids[] = $row[0];
$db->free_result($result);
$show_as = 'topics';
// Final search results
$search_results = implode(',', $search_ids);
// Fill an array with our results and search properties
$temp['search_results'] = $search_results;
$temp['num_hits'] = $num_hits;
$temp['sort_by'] = $sort_by;
$temp['sort_dir'] = $sort_dir;
$temp['show_as'] = $show_as;
$temp = serialize($temp);
$search_id = mt_rand(1, 2147483647);
//set results 
$sort_by_sql = 't.last_post';
$sql = 'SELECT t.id AS tid, t.poster, t.subject, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.forum_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE t.id IN('.$search_results.') GROUP BY t.id, t.poster, t.subject, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.forum_id'.$group_by_sql.' ORDER BY '.$sort_by_sql;
$result = $db->query($sql) or error('Unable to fetch search results', __FILE__, __LINE__, $db->error());
$search_set = array();
while ($row = $db->fetch_assoc($result))
	$search_set[] = $row;
$db->free_result($result);

// Write out feed
echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
echo '	<rss version="2.0">'."\n";
echo '		<channel>'."\n";
echo '			<title>'.$title .'</title>'."\n";
echo '			<link>'.$baseURL.'</link>'."\n";
echo '			<description>'.$desc .' '.count($search_set).'</description>'."\n";
echo '			<language>en</language>'."\n";
echo '			<copyright>© '.$title .'</copyright>'."\n";
echo '			<lastBuildDate>'.date("r").'</lastBuildDate>'."\n";
//loop through items
for ($i = 0; $i < count($search_set); ++$i) {
echo '			<item>'."\n"; 
echo '			<title> '.$search_set[$i]['subject'].' </title> '."\n";
				if ($search_set[$i]['question'] == "")
echo '			<link>'.$baseURL.'/viewtopic.php?id='.$search_set[$i]['tid'].'</link>'."\n";
				else
echo '			<link>'.$baseURL.'/viewpoll.php?id='.$search_set[$i]['tid'].'</link>'."\n";
echo '			<description>'.str_replace("\n", '<br />', pun_htmlspecialchars($search_set[$i]['message'])).'</description> '."\n";
echo'			</item>'."\n";
}

// end feed
echo'		</channel>'."\n";
echo'	</rss>';
}
?>