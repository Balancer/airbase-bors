<?php

	function punbb_forum_updates_make($forum_id)
	{
		$forum_id = intval($forum_id);

		$base = "{$_SERVER['DOCUMENT_ROOT']}/forum/$forum_id";
		@mkdir($base);
		
		include_once("inc.php");
		$updates = make_js(punbb_get_forum_updates($forum_id, 20));
		$fh = fopen("$base/updates.js", "wt");
		fwrite($fh, $updates);
		fclose($fh);
		return $updates;
	}

	function punbb_get_forum_updates($forum_id, $limit = 20)
	{
		$forum_id = intval($forum_id);
		$limit    = intval($limit);

		if(!$limit || $limit > 100)
			$limit = 20;

		$ch = new bors_cache();
		if($ch->get("punbb_all_subforums", $forum_id))
			$forums = unserialize($ch->last());
		else
		{
			$forums = join(", ", punbb_get_all_subforums($forum_id));
			$ch->set(serialize($forums), 3600);
		}

		$topics = array();
		$db = new DataBase('AB_FORUMS');
		if($forums)
			foreach($db->get_array("SELECT id as topic_id, subject as title,  id mod 1000 as sub FROM topics WHERE last_post > ".(time()-90*86400)." AND forum_id IN($forums) ORDER BY last_post DESC LIMIT $limit") as $t)
				$topics[] = $t;

		$data['topics'] = $topics;
		
        include_once("engines/smarty/assign.php");
        return template_assign_data("forum-updates-make.htm", $data);
	}
