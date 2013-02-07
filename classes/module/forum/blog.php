<?php

class module_forum_blog extends base_page
{
	function main_db() { return 'AB_FORUMS'; }

	private $_data = array();
	function local_data()
	{
		$limit = $this->args('limit', 25);
//		$page  = $this->args('limit', 25);

		$page_id = $this->page().','.$limit;

		if(isset($this->_data[$page_id]))
			return $this->_data[$page_id];

		$skip_forums = array(19, 37, 73, 102, 138, 170, 184, 191);
		if($sfs = $this->args('skip_forums'))
			$skip_forums = array_merge($skip_forums, blib_list::parse_condensed($sfs));

// var_dump($skip_forums);

		$where = array(
			'is_public' => true,
			'order' => '-blogged_time',
			'page' => max(1,$this->page()),
			'per_page' => $limit,
		);

		if($fids = $this->arg('forum_ids'))
			$where['forum_id IN'] = blib_list::parse_condensed($fids);
		else
			$where['forum_id NOT IN'] = $skip_forums;

		$where['inner_join'] = 'balancer_board_user ON (balancer_board_user.id = balancer_board_blog.owner_id AND balancer_board_user.is_destructive = 0)';

		$blogs = bors_find_all('balancer_board_blog', $where);

		$x = bors_fields_array_extract($blogs, array('id', 'owner_id', 'forum_id'));
		$posts = bors_find_all('balancer_board_post', array('id IN' => array_filter(array_unique($x['id'])), 'by_id' => true));
		$users = bors_find_all('balancer_board_user', array('id IN' => array_filter(array_unique($x['owner_id'])), 'by_id' => true));
		$forums = bors_find_all('balancer_board_forum', array('id IN' => array_filter(array_unique($x['owner_id'])), 'by_id' => true));
		$topics = bors_find_all('balancer_board_topic', array('id IN' => array_filter(array_unique(bors_field_array_extract($posts, 'topic_id')))));

		$this->_data[$page_id] = array(
				'blog_records' => $blogs,
				'posts' => $posts,
				'no_show_answers' => true,
				'skip_message_footer' => true,
			);
		
		return $this->_data[$page_id];
	}
}
