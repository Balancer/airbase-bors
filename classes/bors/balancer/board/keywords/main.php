<?php

class balancer_board_keywords_main extends base_page
{
	function title() { return ec('Тэги'); }
	function nav_name() { return ec('тэги'); }
	function template() { return 'forum/common.html'; }

	function local_data()
	{
		template_noindex();

		$top = objects_array('common_keyword', array(
				'targets_count>' => 100,
				'order' => '-targets_count',
				'limit' => 20,
				'by_id' => true,
		));

		$tags = objects_array('common_keyword', array(
				'targets_count>' => 2,
				'order' => 'keyword_original',
				'id NOT IN' => array_keys($top),
//				'limit' => '10,-1'
		));

		foreach($top as $id => $x)
		{
			$max = $x->targets_count();
			break;
		}

		foreach($tags as $x)
		{
			$style = array();
			if($bold = $x->targets_count() > sqrt($max)/2)
				$style[] = 'font-weight: bold;';

			$style[] = 'font-size: '.intval(8+sqrt($x->targets_count())).'px;';

			$x->set_attr('style', join(' ', $style));
		}


		return array(
			'keywords_top' => $top,
			'keywords' => $tags,
		);
	}
}
