<?php

class module_user_blog extends base_page
{
	private $user;

	function db_name() { return 'AB_FORUMS'; }

	function body_data()
	{
		$blog_records = bors_find_all('balancer_board_blog', array(
			'owner_id' => $this->args('owner_id'),
			'limit' => $this->args('limit', 5),
			'order' => '-blogged_time',
			'is_public' => 1,
			'by_id' => true,
		));

		$posts = bors_find_all('balancer_board_post', array(
			'id IN' => array_keys($blog_records),
			'by_id' => true,
		));


		foreach($blog_records as $blog_id => $blog)
		{
			if(empty($posts[$blog_id]))
			{
				unset($blog_records[$blog_id]);
				continue;
			}

			$posts[$blog_id]->set_attr('blog', $blog);
			if($kws = $blog->keywords())
				$posts[$blog_id]->set_keyword_links(balancer_blogs_tag::linkify($kws, '', ' ', true), false);
		}

		return array(
			'blog_records' => $blog_records,
			'posts' => $posts,
			'skip_avatar_block' => $this->args('skip_avatar_block', false),
		);
	}
}
