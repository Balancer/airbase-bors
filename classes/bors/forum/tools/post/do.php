<?php

class forum_tools_post_do extends bors_page
{
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }

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

				// Перецепляем все аттачи:
				foreach(bors_find_all('balancer_board_attach', ['post_id' => $post->id()]) as $a)
					balancer_board_posts_object::register_object($post, $a);

				// Стираем превьюшки картинок
				// Ищем все картинки темы:
				if($objects = bors_find_all('balancer_board_posts_object', array(
					'post_id' => $this->id(),
					'target_class_name IN' => array('bors_image', 'airbase_image'),
				)))
				{
					foreach($objects as $obj)
						if($t = $obj->target())
							$t->clear_thumbnails();
				}

				$votes = $post->score();
				if($votes)
				{
					$old_warning = bors_find_first('airbase_user_warning', array(
						'warn_class_id' => $post->class_id(),
						'warn_object_id' => $post->id(),
					));
				}

				config_set('lcml_cache_disable_full', true);
				$post->do_lcml_full_compile();
				$post->set_warning_id(NULL, true);
				$post->set_flag_db(NULL, true);
				if($owner = $post->owner())
					$owner->set_signature_html(NULL);

				echo 'recalc';
				$post->recalculate();
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
				balancer_board_action::add($post, bors()->user()->title().": скрытие сообщения ".$post->titled_link_in_container(), true);
				break;

			case 'show':
				$post->set_is_hidden(NULL);
				balancer_board_action::add($post, bors()->user()->title().": показ сообщения ".$post->titled_link_in_container(), true);
				break;

			default:
				break;
		}

		$post->cache_clean();
		$topic->cache_clean();
		$topic->recalculate();

		return go($post->url_in_topic(NULL, true));
	}
}
