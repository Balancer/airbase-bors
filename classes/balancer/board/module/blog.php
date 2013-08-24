<?php

class balancer_board_module_blog extends balancer_board_module
{
	function body_data()
	{
		$limit = $this->args('limit', 25);

		$skip_forums = array(19, 37, 73, 102, 138, 170);
		if($sfs = $this->args('skip_forums'))
			$skip_forums = array_merge($skip_forums, blib_list::parse_condensed($sfs));

		$where = array(
			'is_public' => true,
			'order' => '-blogged_time',
			'page' => max(1,$this->page()),
			'per_page' => $limit,
		);

		if($cat_names = $this->args('cat_names'))
		{
			$where['forum_id IN'] = balancer_board_category::forums_for_category_names($cat_names)->keys();
			$where['forum_id NOT IN'] = $skip_forums;
		}
		elseif($fids = $this->arg('forum_ids'))
			$where['forum_id IN'] = blib_list::parse_condensed($fids);
		else
			$where['forum_id NOT IN'] = $skip_forums;

		$blogs = bors_find_all('balancer_board_blog', $where);

		$data = array('blog_records' => $blogs);

		$x = bors_fields_array_extract($blogs, array('id', 'owner_id', 'forum_id'));
		$data['posts'] = bors_find_all('balancer_board_post', array('id IN' => array_filter(array_unique($x['id'])), 'by_id' => true));
		$data['users'] = bors_find_all('balancer_board_user', array('id IN' => array_filter(array_unique($x['owner_id'])), 'by_id' => true));
		$data['forums'] = bors_find_all('balancer_board_forum', array('id IN' => array_filter(array_unique($x['owner_id'])), 'by_id' => true));
		$data['topics'] = bors_find_all('balancer_board_topic', array('id IN' => array_filter(array_unique(bors_field_array_extract($data['posts'], 'topic_id')))));
/*
		$this->_data[$page_id] = array(
				'blog_records' => $blogs,
				'posts' => $posts,
				'no_show_answers' => true,
				'skip_message_footer' => true,
			);
*/
		$data['post_args'] = array(
			'template' => 'blog',
			'show_title' => 'container',
			'css' => array(
				'head' => 'breadcrumb',
//				'left' => 'pull-left',
//				'right' => 'pull-right',
			),
		);

		$data['blog_body_template'] = $this->args('blog_body_template', 'blog');

		return $data;
	}
}
