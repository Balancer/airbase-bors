<?php

class balancer_board_topics_images extends balancer_board_topics_blog
{
	function title() { return "Сообщения с картинками в теме «{$this->topic()->title()}»"; }
	function nav_name() { return 'изображения'; }

	function parents() { return [$this->topic()->url()]; }

	function url() { return $this->url_ex($this->default_page()); }

	function url_ex($page = NULL)
	{
		return $this->topic()->category()->url()
			.date("Y/m", $this->topic()->create_time())."/t{$this->id()}/images"
			.(is_null($page) || $page == $this->default_page() ? '' : "/{$page}.html");
	}

	function order() { return 'create_time'; }
	function is_reversed() { return false; }

	function where()
	{
//		return array_merge(parent::where(), array(
		return array(
			'topic_id' => $this->id(),
			'left_join' => array(
				'board_objects x ON x.post_id = `posts`.id',
				'attach_2_files a ON a.post_id = `posts`.id',
			),
			'((x.post_id IS NOT NULL AND x.target_class_name IN ("airbase_image"))
				OR (a.post_id IS NOT NULL AND a.extension IN ("JPG", "PNG", "JPEG", "GIF")))',
			'group' => 'posts.id',
/*			'(source LIKE "%jpg%"
				OR source LIKE "%png%"
				OR source LIKE "%img%"
				OR source LIKE "%gif%")',
*/
		);
	}

	function pre_show()
	{
		$forum = $this->topic()->forum();
		if(!$forum || !$forum->can_read())
		{
			template_noindex();
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");
		}

		return parent::pre_show();
	}
}
