<?php

require_once('engines/smarty/assign.php');

    function get_site_news($limit = 10)
    {
        $ch = new bors_cache();

        if($ch->get('sitenews-v3', $limit))
			return $ch->last();

		$limit = intval(max(1,min($limit,100)));

//		set_loglevel(10, NULL);
		$news = bors_find_all('balancer_board_topic', array('forum_id=' => 2, 'order' => '-posted', 'limit' => $limit));
//		set_loglevel(2);
//		exit();

		return $ch->set(template_assign_data("sitenews.html", array('news' => $news)), 600);
    }
