<?php

class balancer_board_keywords_list extends base_page
{
	function title() { return ec('Теги'); }
	function nav_name() { return ec('теги'); }
//	function template() { return 'forum/common.html'; }
	function template() { return 'forum/_header.html'; }

	function local_data()
	{
		template_noindex();

		$top = objects_array('common_keyword', array(
				'target_containers_count>' => 100,
				'order' => '-target_containers_count',
				'limit' => 20,
				'by_id' => true,
		));

		$tags = objects_array('common_keyword', array(
				'target_containers_count>' => 1,
				'order' => 'keyword_original',
				'id NOT IN' => array_keys($top),
//				'limit' => '10,-1'
		));

		foreach($top as $id => $x)
		{
			$max = $x->target_containers_count();
			break;
		}

		foreach($tags as $x)
		{
			$style = array();
			if($bold = $x->target_containers_count() > sqrt($max)/2)
				$style[] = 'font-weight: bold;';

			$style[] = 'font-size: '.intval(8+pow($x->target_containers_count(), 1/2.5)).'px;';

			$x->set_attr('style', join(' ', $style));
			$x->set_attr('titled_link', $x->titled_link_ex(array('popup' => $x->target_containers_count())));
		}


		return array(
			'keywords_top' => $top,
			'keywords' => $tags,
		);
	}
}
