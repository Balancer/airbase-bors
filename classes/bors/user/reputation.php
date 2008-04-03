<?php

class user_reputation extends base_page_db
{
	function storage_engine() { return 'storage_db_mysql'; }

	var $user;
	
	function title() { return $this->user->title().ec(": Репутация"); }
	function nav_name() { return ec("репутация"); }

	function parents() { return array("forum_user://".$this->id()); }

	function __construct($id)
	{
		$this->user = class_load('forum_user', $id);
		parent::__construct($id);

		if(!$id)
			return ec("Не задан ID пользователя.");

//		debug_exit("id={$id}, user={$this->user->id}, page={$this->page()}");
	}

	function default_page() { return $this->total_pages(); }
	
	function preParseProcess($get)
	{
		if(!bors()->user() && !empty($get))
			return go($this->url());

		return false;
	}

	function data_providers()
	{
		$dbu = &new DataBase('USERS');
		$dbf = &new DataBase('punbb');
		
		return array(
			'ref' => @$_SERVER['HTTP_REFERER'],
			'list' => array_reverse(objects_array('airbase_user_reputation', array('user_id=' => $this->id(), 'order' => 'time', 'page'=> $this->page(), 'per_page' => $this->items_per_page()))),
			'reputation_abs_value' => sprintf("%.2f", $dbf->get("SELECT reputation FROM users WHERE id = {$this->id()}")),
			'plus' => $dbu->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = {$this->id()} AND score > 0"),
			'minus' => $dbu->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = {$this->id()} AND score < 0"),
			'user_id' => $this->id(),
		);
	}

	private $total;
	function total_items()
	{
		if($this->total == NULL)
			$this->total = intval(objects_count('airbase_user_reputation', array('where' => array('user_id=' => $this->id()))));

		return $this->total;
	}

//	function url($page=1) { return "http://balancer.ru/user/".$this->id()."/reputation".($page && $page != 1 ? ','.$page : '').".html"; }
	function url($page = 0)
	{	
		if($page == 0 || $this->total_pages() == 1)
			return "http://balancer.ru/user/".$this->id()."/reputation.html"; 
		else
			return "http://balancer.ru/user/".$this->id()."/reputation,{$page}.html"; 
	}

	function cache_static() { return 86400*30; }
		
	function template() { return "forum/common.html"; }

	function can_be_empty() { return true; }
	function can_cached() { return false; }

	function cache_groups() { return "user-{$this->id()}-reputation"; }

	function on_action_reputation_add_do($data)
	{
		$uid = intval($_POST['user_id']);
		if(!$uid)
			return bors_message(ec("Не задан ID пользователя."));

		$me = bors()->user();
		$dbf = &new DataBase('punbb');
		$dbu = &new DataBase('USERS');
		$me_id = $me->id();
		
		if($me_id == 1)
			return bors_message(ec("Голосование возможно только для авторизованных пользователей."));

		if($me->is_banned())
			return bors_message(ec('Вы не можете изменять репутацию  по причине запрета общения на форуме.'));

		if($me_id == $uid)
			return bors_message(ec("Нельзя ставить репутацию самому себе."));
		
		if($dbf->get("SELECT num_posts FROM users WHERE id=$me_id") < 50)
			return bors_message(ec("Репутацию выставлять могут только участники, имеющие более 50 сообщений на форуме."));

		$dbu->insert('reputation_votes', array(
			'user_id'		=> $uid,
			'time'			=> time(),
			'score'			=> $_POST['score'] > 0 ? 1 : -1,
			'voter_id'		=> $me_id,
			'uri'			=> $_POST['uri'],
			'comment'		=> $_POST['comment'],
		));

		$grw = array(
				1 => 8, // admin
				2 => 6, // moder
				3 => 0, // guest
				5 => 4, // coordin
				6 => 2, // старожилы
				21 => 4, // координатор-литератор
			);

		$total = 0;
		foreach($dbu->get_array("SELECT voter_id as id, SUM(score) as sum FROM `reputation_votes` WHERE user_id = $uid GROUP BY voter_id") as $v)
		{
			$reput = (atan($dbf->get("SELECT reputation FROM users WHERE id={$v['id']}"))*2/pi() + 1)/2;
			$group = $dbf->get("SELECT group_id FROM users WHERE id={$v['id']}");
				
			$weight = @$grw[$group];
			if(!$weight)
				$weight = 1;

			if($v['id'] == 10000)
				$weight = 10;
					
			if($dbf->get("SELECT num_posts FROM users WHERE id={$v['id']}") < 50)
				$weight = 0;
				
			$sum = atan($v['sum'])*2/pi() * $weight * $reput;
			$total += $sum;
		}

		$dbf->query("UPDATE users SET reputation = '".str_replace(",",".",$total)."' WHERE id = $uid");

		class_load('forum_user', $uid)->cache_clean_self();
		class_load('cache_group', "user-{$uid}-reputation")->clean();
		
		include_once("inc/navigation.php");
		go("http://balancer.ru/user/$uid/reputation.html?");
	}
	
	function access() { return $this; }
	
	function can_action() { return $_GET['act'] == 'reputation_add_do'; }
	function can_read() { return true; }
	function static_get_cache() { return true; }
}
