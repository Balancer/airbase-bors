<?php

require_once("classes/bors/BorsBaseDbPage.php");

class user_reputation extends BorsBaseDbPage
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

		$db = &new DataBase('USERS');
		
		$this->add_template_data('ref', @$_SERVER['HTTP_REFERER']);
		
		$this->add_template_data('list', $db->get_array("SELECT * FROM reputation_votes WHERE user_id = $id ORDER BY time DESC"));

		$dbf = &new DataBase('punbb');
		$this->add_template_data('reputation_abs_value', sprintf("%.2f", $dbf->get("SELECT reputation FROM users WHERE id = $id")));
		
		$this->add_template_data('plus', $db->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = $id AND score > 0"));
		$this->add_template_data('minus', $db->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = $id AND score < 0"));

		$this->add_template_data('user_id', $id);
	}

	function url() { return "http://balancer.ru/user/".$this->id()."/reputation.html"; }

	function cache_static() { return 86400*30; }
		
	function template() { return "forum/forum.html"; }

	function can_be_empty() { return true; }
}
