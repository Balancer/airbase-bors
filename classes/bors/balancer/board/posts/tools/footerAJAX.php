<?php

class balancer_board_posts_tools_footerAJAX extends base_page
{
	function object() { return $this->load_attr('object', object_load($this->id())); }
	function template() { return 'null.html'; }

	function pre_show()
	{
		if(!bors()->user_id())
			return "Только для зарегистрированных пользователей!";
		
		return false;
	}

	function local_data()
	{
		$x = $this->object();
		$over = $x ? bors_overquote_rate($x->source()) : NULL;
	
		return array(
			'p' => $x,
			'overquote' => $over,
			'overquote_crit' => ($over > 60),
			'id' => $x ? $x->id() : 0,
			'owner_id' => $x->owner_id(),
		);
	}
}

function bors_overquote_rate($text)
{
	if(bors_strlen($text) <= 400)
		return 0;
	
	$q = 0;
	$a = 0;
	foreach(explode("\n", $text) as $s)
	{
		$s = trim($s);
		if($s == '')
			continue;
		if(preg_match('/^\S+>/', $s))
			$q += bors_strlen($s);
		else
			$a += bors_strlen($s);
	}

	if($a == 0)
		return 100;

	return sprintf('%.1f', $q/($a+$q)*100);
}
