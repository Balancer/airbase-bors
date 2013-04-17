<?php

class balancer_club_main extends balancer_page
{
	var $title_ec = 'Клуб Balancer.ru';
	var $nav_name_ec = 'клуб';

	function create_time() { return 1319150670; }
//	function parents() { return array('http://www.balancer.ru/community/'); }
	function config_class() { return 'balancer_board_config'; }

	static function cat_names() { return "balancer_nt,lbr,l2f,balancer_club,balancer_socionics,bionco"; }
	static function subforums() { return balancer_board_category::forums_for_category_names(self::cat_names()); }

    function pre_show()
    {
        template_rss(config('main_site_url').'/club/rss.xml', ec('Обновления блога Клуба'));
        return parent::pre_show();
    }
}
