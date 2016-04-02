<?php

require_once('inc/clients/geoip-place.php');

class balancer_board_posts_tools_footerAJAX extends balancer_board_page
{
	function object() { return $this->__havec('object') ? $this->__lastc() : $this->__setc(bors_load($this->id())); }
	function template() { return 'null.html'; }

	function pre_show()
	{
		template_nocache();

		if(!bors()->user_id())
			return "Только для зарегистрированных пользователей!";

		return false;
	}

	function body_data()
	{
		$x = $this->object();
		$over = $x ? bors_overquote_rate($x->source()) : NULL;
/*
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
*/

		$warning = $x ? bors_find_first('airbase_user_warning', ['warn_object_id' => $x->id()]) : NULL;

		$data = array(
			'p' => $x,
			'overquote' => $over,
			'overquote_crit' => ($over > 60),
			'id' => $x ? $x->id() : 0,
			'owner_id' => $x ? $x->owner_id() : NULL,
			'owner' => $x ? $x->owner() : NULL,
			'spam' => object_property($x, 'is_spam'),
			'warning' => $warning,
			'rel_to' => $x ? balancer_board_users_relation::find(['from_user_id' => bors()->user_id(), 'to_user_id' => $x->owner_id()])->first() : NULL,
		);

		if($warning && $x
				&& bors()->user()->rpg_level() >= $x->owner()->rpg_level() - 2
				&& bors()->user()->warnings() <= 0
		)
		{
//			var_dump($warning);
			$new_score = max(1, pow(3, $x->owner()->rpg_level() - bors()->user()->rpg_level()));
			if($new_score < 10)
			{
				$data['can_take_warning'] = true;
				$data['take_warning_score'] = $new_score;
			}
		}

		return array_merge(parent::body_data(), $data);
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
