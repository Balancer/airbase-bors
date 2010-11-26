<?php

class airbase_user_aliases extends base_page
{
	function title() { return ec('Пользователи, писавшие с тех же IP, что и ').$this->user()->title().ec(' за крайние 30 дней'); }
	function config_class() { return 'airbase_forum_config'; }
	function user() { return object_load('bors_user', $this->id()); }
	function can_be_empty() { return false; }
	function loaded() { $this->init(); return $this->user() != NULL; }
	function local_data()
	{
		template_noindex();

		$last_post = $this->db('punbb')->select('posts', 'MAX(posted)', array('poster_id' => $this->id())) + 1;
		$depth = $last_post-86400*30;

		$ips = array_filter($this->db('punbb')->select_array('posts', 'distinct(poster_ip)', array('poster_id' => $this->id(), "posted BETWEEN $depth AND $last_post")));

		if($ips)
			$users_list = $this->db('punbb')->select_array('posts', 'poster_id, count(*) as count', array(
				'poster_ip IN' => "'".join("','",$ips)."'", 
				"posted BETWEEN $depth AND $last_post",
				'group' => 'poster_id', 
				'order' => '-count')
		);
		return array('users_list' => $ips ? $users_list : array());
	}

	function cache_static() { return config('static_forum') ? rand(3600, 7200) : 0; }

	function pre_show()
	{
		if(bors()->client()->is_bot())
			return go('/', true);

		return false;
	}
}
