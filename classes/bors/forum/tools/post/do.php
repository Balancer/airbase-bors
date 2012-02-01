<?php

class forum_tools_post_do extends base_page
{
	function main_db() { return config('punbb.database', 'punbb'); }

	function can_cache() { return false; }

	function pre_show()
	{
		$post = bors_load('balancer_board_post', intval($this->id()));
		$topic = $post->topic();

		if(!bors()->user() || !bors()->user()->group()->is_coordinator())
			return bors_message(ec('У Вас нет прав для выполнения этой операции'));

		config_set('lcml_cache_disable', true);

		switch($this->page())
		{
			case 'drop-cache':
				config_set('lcml_cache_disable_full', true);
				$post->set_post_body(NULL, true);
				$post->set_warning_id(NULL, true);
				$post->set_flag_db(NULL, true);
				$post->owner()->set_signature_html(NULL);
				$post->cache_clean();
				$post->store();
				$post->body();

				$topic->cache_clean();
				$topic->set_modify_time(time(), true);
				$topic->store();

				$blog = bors_load('balancer_board_blog', $this->id());

				if($blog)
					$blog->recalculate($post, $topic);

				break;

			case 'pinned':
				$post->set_sort_order(-100, true);
				break;

			case 'unpinned':
				$post->set_sort_order(NULL, true);
				break;

			case 'hide':
				$post->set_is_hidden(true);
				break;

			case 'show':
				$post->set_is_hidden(NULL);
				break;

			default:
				break;
		}

		$post->cache_clean();
		$topic->cache_clean();
		$topic->recalculate();

		return go($post->url_for_igo());
	}
}
