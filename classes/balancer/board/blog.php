<?php

class balancer_board_blog extends forum_blog
{
	function extends_class() { return 'forum_blog'; }

	static function create($post, $keywords_string)
	{
		$topic = $post->topic();
		$user  = $post->owner();

		$blog = object_new_instance('balancer_board_blog', array(
			'id'        => $post->id(),
			'owner_id'  => $user->id(),
			'topic_id'  => $topic->id(),
			'forum_id'  => $topic->forum_id(),
			'is_public' => $topic->is_public(),
		));

		if($keywords_string)
			$blog->set_keywords_string($keywords_string, true);

		common_keyword_bind::add($blog);

		return $blog;
	}
}
