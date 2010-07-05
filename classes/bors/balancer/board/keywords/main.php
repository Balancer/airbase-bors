<?php

class balancer_board_keywords_main extends base_page
{
	function title() { return ec('Тэги'); }
	function template() { return 'forum/common.html'; }
	
	function local_data()
	{
		template_noindex();
	
		return array(
			'keywords' => objects_array('common_keyword', array(
				'targets_count>' => 0,
				'order' => '-targets_count'
			)),
		);
	}
}
