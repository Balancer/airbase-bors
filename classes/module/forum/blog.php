<?php

class module_forum_blog extends base_page
{
	function main_db() { return 'punbb'; }

	private $_data = array();
	function local_data()
	{
		$limit = $this->args('limit', 25);
//		$page  = $this->args('limit', 25);

		$page_id = $this->page().','.$limit;

		if(isset($this->_data[$page_id]))
			return $this->_data[$page_id];

		$skip_forums = array(19, 37, 73, 102, 138, 170);
		if($sfs = $this->args('skip_forums'))
			$skip_forums = array_merge($skip_forums, blib_list::parse_condensed($sfs));

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

		$blogs = objects_array('forum_blog', $where);

		$x = bors_fields_array_extract($blogs, array('id', 'owner_id', 'forum_id'));
		$posts = objects_array('forum_post', array('id IN' => array_filter(array_unique($x['id'])), 'by_id' => true));
		$users = objects_array('balancer_board_user', array('id IN' => array_filter(array_unique($x['owner_id'])), 'by_id' => true));
		$forums = objects_array('forum_forum', array('id IN' => array_filter(array_unique($x['owner_id'])), 'by_id' => true));
		$topics = objects_array('forum_topic', array('id IN' => array_filter(array_unique(bors_field_array_extract($posts, 'topic_id')))));

		$this->_data[$page_id] = array(
				'blog_records' => $blogs,
				'posts' => $posts,
				'no_show_answers' => true,
				'skip_message_footer' => true,
			);
		
		return $this->_data[$page_id];
	}
}
