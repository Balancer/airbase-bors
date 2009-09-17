<?php

class airbase_user_topics extends base_page
{
//	function config_class() { return 'airbase_forum_config'; }
	function main_db_storage() { return 'punbb'; }
	function template() { return 'forum/_header.html'; }

	private $ids = false;
	private function topics_ids()
	{
		if($this->ids === false)
			$this->ids  = $this->db()->select_array('posts', 'DISTINCT topic_id', array(
				'poster_id=' => $this->id(),
				'posted > ' => time()-86400*31,
			));

		return $this->ids;
	}

	function url() { return "http://balancer.ru/user/".$this->id()."/use-topics.html"; }

	function local_template_data_set()
	{
		if($this->topics_ids())
			return array('topics' => objects_array('forum_topic', array(
					'id IN' => $this->topics_ids(), 
					'order' => '-last_post',
			)));
		else
			return array();
	}

	function pre_show()
	{
		$this->add_template_data('skip_subforums', true);
		templates_noindex();
		return false;
	}

	private $user = false;
	function user() { if($this->user === false) $this->user = object_load('bors_user', $this->id()); return $this->user; }
	function title() { return $this->user()->title().ec(': темы с участием за месяц'); }
	function nav_name() { return ec('темы с участием за месяц'); }

	function body_template() { return 'xfile:airbase/forum/forum.html'; }

	function cache_static() { return rand(86400*7, 14*86400); }

	function pages_links_nul() { return ""; }
}
