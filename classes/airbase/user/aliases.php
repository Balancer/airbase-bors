<?php

class airbase_user_aliases extends balancer_board_page
{
	function title() { return ec('Пользователи, писавшие с тех же IP, что и ').$this->user()->title().ec(' c ').date('d.m.Y', $this->last_post_time()-86400*30).ec(' по ').date('d.m.Y', $this->last_post_time()); }
	function config_class() { return 'airbase_forum_config'; }
	function user() { return bors_load('balancer_board_user', $this->id()); }
	function can_be_empty() { return false; }
	function is_loaded() { $this->data_load(); return $this->user() != NULL; }

	function last_post_time()
	{
		if($this->__havefc())
			return $this->__lastc();

		return $this->__setc(driver_mysql::factory(config('punbb.database'))->select('posts', 'MAX(posted)', array('poster_id' => $this->id())) + 1);
	}

	function begin() { return $this->last_post_time()-86400*30; }

	function body_data()
	{
		template_noindex();
		$last_post = $this->last_post_time();

		$depth = $last_post-86400*30;

		$ips = array_filter(driver_mysql::factory(config('punbb.database'))->select_array('posts', 'distinct(poster_ip)', array('poster_id' => $this->id(), "posted BETWEEN $depth AND $last_post")));

		if($ips)
			$users_list = driver_mysql::factory(config('punbb.database'))->select_array('posts', 'poster_id, count(*) as count', array(
				'poster_ip IN' => "'".join("','",$ips)."'",
				"posted BETWEEN $depth AND $last_post",
				'group' => 'poster_id', 
				'order' => '-count')
		);

		return array(
			'users_list' => $ips ? $users_list : array(),
			'ips_where' => array('poster_ip IN' => $ips, 'posted BETWEEN' => array($this->begin(), $this->last_post_time())),
		);
	}

	function cache_static() { return config('static_forum') ? rand(3600, 7200) : 0; }

	function pre_show()
	{
		if(bors()->client()->is_bot())
			return go('/', true);

		return false;
	}
}
