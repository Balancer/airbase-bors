<?php

class balancer_board_mobile_category extends balancer_board_category
{
	function extends_class() { return 'forum_category'; }

	function url() { return '/c'.$this->id(); }

	function parents() { return array('/'); }
}
