<?php

class user_reputation extends base_page_db
{
	function _class_file() { return __FILE__; }

	var $user;
	var $ref;
	
	function title() { return $this->user->title().ec(": Репутация"); }
	function nav_name() { return ec("репутация"); }

	function parents() { return array("forum_user://".$this->id()); }

	function __construct($id)
	{
		$this->user = class_load('forum_user', $id);
		parent::__construct($id);

		if(!$id)
			return ec("Не задан ID пользователя.");

//		echo "id={$id}, user={$this->user->id}";
	}

	function data_providers()
	{
		$dbu = &new DataBase('USERS');
		$dbf = &new DataBase('punbb');
		
		return array(
			'ref' => @$_SERVER['HTTP_REFERER'],
			'list' => $dbu->get_array("SELECT * FROM reputation_votes WHERE user_id = {$this->id()} ORDER BY time DESC"),
			'reputation_abs_value' => sprintf("%.2f", $dbf->get("SELECT reputation FROM users WHERE id = {$this->id()}")),
			'plus' => $dbu->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = {$this->id()} AND score > 0"),
			'minus' => $dbu->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = {$this->id()} AND score < 0"),
			'user_id' => $this->id(),
		);
	}

	function url() { return "http://balancer.ru/user/".$this->id()."/reputation.html"; }

	function cache_static() { return 600; }
		
	function template() { return "forum/common.html"; }

	function can_be_empty() { return true; }
	function can_cached() { return false; }
}
