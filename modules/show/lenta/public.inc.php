<?
    function module_show_lenta_public($forums, $limit = 15)
    {
//		include_once("funcs/texts.php");
		include_once("inc/lists.php");
		include_once("other/punbb-modified-forum/include/pun_bal.php");

		$db = new driver_mysql(config('punbb.database'));

		$topics = array();
		foreach($db->get_array("
			SELECT 
				t.*, 
				m.html as message, 
				f.forum_name, 
				u.use_avatar, 
				u.avatar_width, 
				u.avatar_height, 
				u.title as u_title, 
				g.g_user_title,
				u.id as owner_id
			FROM `topics` t 
				LEFT JOIN `messages` m ON (m.id = t.first_pid) 
				LEFT JOIN `forums` f ON (t.forum_id = f.id) 
				LEFT JOIN `users` u ON (t.poster_id = u.id) 
				LEFT JOIN `groups` g ON (g.g_id = u.group_id) 
			WHERE t.forum_id IN (".join(",", blib_list::parse_condensed($forums)).")
				AND t.forum_id NOT IN (19,1,37,39,170,108,138,178)
				AND moved_to IS  NULL 
			ORDER BY t.posted DESC LIMIT ".intval($limit)) as $t)
		{
			if(!$t['message'])
				$db->update('messages', "id = {$t['first_pid']}", array(
					'html' => $t['message'] = pun_lcml($db->get("SELECT message FROM messages WHERE id = {$t['first_pid']}"))
				));

//			if($t['more'] = strlen($t['message']) > 1024)
//				$t['message'] = strip_text($t['message'], 1024);

			$topics[] = $t;
		}
		
		include_once("engines/smarty/assign.php");
		return template_assign_data("public.html", array('topics'=>$topics));
    }
