<?php

require_once('inc/clients/geoip-place.php');

class balancer_board_posts_tools_footerAJAX extends base_page
{
	function object() { return $this->__havec('object') ? $this->__lastc() : $this->__setc(object_load($this->id())); }
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

		if(is_null($x->is_spam()) && (!$x->owner() || ($x->owner()->num_posts() < 20 && $x->owner()->create_time() > time() - 7*86400)))
		{
			$x->set_is_spam(balancer_akismet::factory()->classify($x) ? 1 : 0, true);

			if($x->is_spam())
			{
				if($x->owner())
					debug_hidden_log('spam-ajax', "Marked as spam: [owner={$x->owner()}, num_posts={$x->owner()->num_posts()}, registered={$x->owner()->create_time()}]".$x->source());
				else
					debug_hidden_log('spam-ajax', "Marked as spam: [owner={$x->owner()}]".$x->source());
			}
		}

		return array(
			'p' => $x,
			'overquote' => $over,
			'overquote_crit' => ($over > 60),
			'id' => $x ? $x->id() : 0,
			'owner_id' => $x ? $x->owner_id() : NULL,
			'spam' => object_property($x, 'is_spam'),
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
