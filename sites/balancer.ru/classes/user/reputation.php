<?php

class user_reputation extends base_page
{
	var $user;

	function title() { return $this->user ? $this->user->title().ec(": Репутация") : ''; }
	function nav_name() { return ec("репутация"); }

	function parents() { return array("balancer_board_user://".$this->id()); }

	function __construct($id)
	{
		$this->user = class_load('balancer_board_user', $id);
		parent::__construct($id);

//		debug_exit("id={$id}, user={$this->user->id}, page={$this->page()}");
	}

	function default_page() { return $this->total_pages(); }

	function pre_parse($get)
	{
		if(!$this->id())
			return bors_message(ec("Не задан ID пользователя."));

		if(!bors()->user() && !empty($get))
			return go($this->url());

		return false;
	}

	function local_data()
	{
		template_noindex();

		$dbf = new DataBase('punbb');

		$list = array_reverse(objects_array('airbase_user_reputation', array(
			'user_id' => $this->id(),
			'is_deleted' => 0,
			'order' => 'time',
			'page'=> $this->page(),
			'per_page' => $this->items_per_page()))
		);

		for($i=0; $i<count($list); $i++)
			if($r = $list[$i]->refer())
				$list[$i]->set('target', object_load($r), false);

		return array(
			'ref' => $this->ref() ? $this->ref() : @$_SERVER['HTTP_REFERER'],
			'list' => $list,
			'reputation_abs_value' => sprintf("%.2f", $dbf->get("SELECT reputation FROM users WHERE id = {$this->id()}")),
			'pure_reputation' => sprintf("%.2f", $dbf->get("SELECT pure_reputation FROM users WHERE id = {$this->id()}")),
			'plus' => objects_count('airbase_user_reputation', array('user_id' => $this->id(), 'score>=' => 0)),
			'minus' => objects_count('airbase_user_reputation', array('user_id' => $this->id(), 'score<' => 0)),
			'user_id' => $this->id(),
		);
	}

	private $total;
	function total_items()
	{
		if($this->total == NULL)
			$this->total = intval(objects_count('airbase_user_reputation', array(
				'user_id=' => $this->id(),
				'is_deleted' => 0,
			)));

		return $this->total;
	}

	function ref()
	{
		if($ref = $this->args('ref'))
			return $ref;

		$keys = array_keys($_GET);
		if(!empty($keys[0]) && (preg_match('/^http:/', $keys[0]) || preg_match('/^\w+$/', $keys[0])))
			return $keys[0];

		return NULL;
	}

//	function url($page=1) { return "http://balancer.ru/user/".$this->id()."/reputation".($page && $page != 1 ? ','.$page : '').".html"; }
	function url($page = 0, $append_query = true)
	{
		if($page == 0 || $this->total_pages() == 1)
			$url = "http://balancer.ru/user/".intval($this->id())."/reputation.html";
		else
			$url = "http://balancer.ru/user/".intval($this->id())."/reputation,{$page}.html";

		if($append_query && $this->ref())
			$url .= '?'.$this->ref();

		return $url;
	}

	function cache_static() { return config('static_forum') ? rand(86400*7, 86400*30) : 0; }

	function template() { return "forum/common.html"; }

	function can_be_empty() { return true; }
	function can_cached() { return false; }

	function cache_groups() { return "user-{$this->id()}-reputation"; }

	function on_action_reputation_add_do($data)
	{
		require_once('inc/users.php');

		$uid = intval(@$_POST['user_id']);
		if(!$uid)
			return bors_message(ec("Не задан ID пользователя."));

		$me = bors()->user();
		$dbf = new DataBase('punbb');
		$dbu = new DataBase('USERS');
		$me_id = $me->id();

		if($me_id == 1)
			return bors_message(ec("Голосование возможно только для авторизованных пользователей."));

		if($me->is_banned())
			return bors_message(ec('Вы не можете изменять репутацию  по причине запрета общения на форуме.'));

		if($me_id == $uid)
			return bors_message(ec("Нельзя ставить репутацию самому себе."));

		$md = md5($_POST['comment'].$uid);
		if($me->last_message_md() == $md)
			return bors_message(ec('Вы уже отправили это сообщение'));
		
		$me->set_last_message_md($md, true);
	
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
				1 => 7, // admin
				2 => 5, // moder
				3 => 0, // guest
				5 => 3, // coordinator
				6 => 2, // старожилы
				21 => 3, // координатор-литератор
			);

		$total = 0;
		foreach($dbu->get_array("SELECT voter_id as id, SUM(score) as sum FROM `reputation_votes` WHERE user_id = $uid GROUP BY voter_id") as $v)
		{
			$reput = bors_user_reputation_weight($dbf->get("SELECT reputation FROM users WHERE id={$v['id']}"));
			$group = $dbf->get("SELECT group_id FROM users WHERE id={$v['id']}");
				
			$weight = @$grw[$group];
			if(!$weight)
				$weight = 1;

			if($v['id'] == 10000)
				$weight = 9;
					
			if($dbf->get("SELECT num_posts FROM users WHERE id={$v['id']}") < 50)
				$weight = 0;
				
			$sum = atan($v['sum'])*2/pi() * $weight * $reput;
			$total += $sum;
		}

		$dbf->query("UPDATE users SET reputation = '".str_replace(",",".",$total)."' WHERE id = $uid");

		$target_user = class_load('balancer_board_user', $uid);

		foreach (glob($target_user->user_dir().'/reputation*.html') as $filename)
			unlink($filename);

		$target_user->cache_clean_self();
//		class_load('cache_group', "user-{$uid}-reputation")->clean();
		
		include_once("inc/navigation.php");
		return go($this->url($this->total_pages(), false));
	}
	
	function access() { return $this; }
	
	function can_action() { return $_GET['act'] == 'reputation_add_do'; }
	function can_read() { return true; }
	function static_get_cache() { return true; }

	function cache_clean_self()
	{
//		echo "Clean cache in {$this->cache_dir()}";
//		exit();
		parent::cache_clean_self();
		foreach(glob($this->cache_dir().'/reputation*.html') as $f)
			@unlink($f);
	}
}
