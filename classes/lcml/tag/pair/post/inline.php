<?php

class lcml_tag_pair_post_inline extends bors_lcml_tag_pair
{
	function html($content, &$params = array())
	{
		$post_id = $content;
		$post = bors_load('balancer_board_post', $post_id);
		$html = $post->titled_link_in_container();

		return $html;
	}
}
