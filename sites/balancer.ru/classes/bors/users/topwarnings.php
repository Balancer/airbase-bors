<?php

class users_topwarnings extends base_page
{
	function main_db_storage(){ return 'punbb'; }

	function template()
	{
		templates_noindex();
		return 'forum/_header.html';
	}

	function title() { return ec("Штрафы пользователей форума"); }
	function nav_name() { return ec("штрафы"); }

	function parents() { return array("http://balancer.ru/users/"); }

	function data_providers()
	{
/*		$warns = array();
		foreach($this->db('punbb')->select_array('warnings', 'user_id, sum(score) as `sum`', array(
					'time>' => time()-30*86400,
					'group' => 'user_id',
				)) as $x)
			$warns[$x['user_id']] = $x['sum'];

		$warns2 = array();
		foreach(objects_array('forum_user', array('id IN' => array_keys($warns))) as $u)
			$warns2[$u->id()] = $u->warnings_rate(31, 'per_posts_and_time');
		
//		asort($warns2);
//		print_d($warns2);
//		exit();
		$top_warn_relative_users = array();
		foreach(array_slice($warns2, 0, 50) as $uid => $w)
			$top_warn_reltive_users[$uid] = array('u' => object_load('forum_user', $uid), 'w' => $w);
*/
		return array(
			'warnings_last' => objects_array('airbase_user_warning', array('order' => '-time', 'limit' => 25)),
			'top_warn_users' => objects_array('forum_user', array('last_post - registered >' => 86400*7, 'order' => '86400.0*warnings_total/(last_post - registered) DESC', 'limit' => 50)),
			'top_warn_relative_users' => @$top_warn_reltive_users,
		);
	}

	function url() { return "http://balancer.ru/users/warnings/"; }

	function cache_static() { return 86400*14; }
}
