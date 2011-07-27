<?php

class balancer_board_forum extends forum_forum
{
	function extends_class_name() { return 'forum_forum'; }

	function last_topics($limit)
	{
		return bors_find_all('balancer_board_topic', array(
			'forum_id' => $this->id(),
			'order' => '-last_post_create_time',
			'limit' => $limit,
		));
	}

	function full_name($forums = NULL, $cats = NULL)
	{
		$result = array();
		$current_forum = $this;
		do {
			$result[] = $current_forum->nav_name();
			if($parent = $current_forum->parent_forum_id())
				$current_forum = $forums ? $forums[$parent] : object_load('airbase_forum_forum', $parent);
		} while($parent);

		$cat = $cats ? $cats[$current_forum->category_id()] : $current_forum->category();

		return join(' « ', $result).' « '.$cat->full_name();
	}
}
