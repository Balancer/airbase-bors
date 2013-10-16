<?php

class lcml_tag_pair_post extends bors_lcml_tag_pair
{
	function html($content, &$params = array())
	{
		$post_id = $content;

//		if(config('is_developer'))
		{
			$post = bors_load('balancer_board_post', $post_id);
			if($params['is_alone'])
				$html = bors_lcml_helper::box($post, array(
					'url' => $post->url_for_igo(),
					'description' => $post->snip(500),
					'reference' => $post->topic()->forum()->titled_link(),
				));
			else
				$html = $post->titled_link_in_container();

//			var_dump($html, $params); exit();
		}

		return $html;
	}
}
