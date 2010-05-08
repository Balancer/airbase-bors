<?php

class user_blog_rss extends base_rss
{
	function title() { return $this->user()->title().ec(": Блог"); }
	function description() { return ec("Все темы, начатые пользователем ").$this->user()->title().ec(" за последние 30 дней. Не более 25 штук."); }

	function user() { return object_load('bors_user', $this->id()); }
	function blog() { return object_load('user_blog', $this->id()); }

	function rss_items()
	{
		return objects_array('forum_blog', array(
			'where' => array('owner_id=' => $this->id()),
			'order' => '-blogged_time',
			'create_time>' => time()-31*86400,
			'limit' => 25,
		));
	}

	function cache_static() { return rand(600, 1200); }

	function url() { return $this->blog()->url().'rss.xml'; }
	function rss_url() { return $this->blog()->url(); }
}

