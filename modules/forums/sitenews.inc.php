<?php
    function get_site_news($limit = 10)
    {
        $ch = &new Cache();
		
        if($ch->get('sitenews-v1', $limit))
			return $ch->last();

		$limit = intval(max(1,min($limit,100)));

//		set_loglevel(10, NULL);
		$news = objects_array('forum_topic', array('forum_id=' => 2, 'order' => '-posted', 'limit' => $limit));
//		set_loglevel(2);
//		exit();

		return $ch->set(template_assign_data("sitenews.html", array('news' => $news)), 3600);
    }
