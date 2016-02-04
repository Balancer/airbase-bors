<?php

require_once('inc/lists.php');

class module_forum_lenta_public extends bors_module
{
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }

	private $bdata = array();
	function body_data()
	{
		$limit = $this->args('limit', 25);
		$page_id = $this->page().','.$limit;

		if(isset($this->bdata[$page_id]))
			return $this->bdata[$page_id];

		$where = array(
			'order' => '-blogged_time',
			'page' => max(1, $this->page()),
			'per_page' => $limit,
			'forum_id NOT IN' => config('forums_private'),
		);

		if($forums = $this->args('forums'))
			$where['forum_id IN'] = blib_list::parse_condensed($forums);

		$this->bdata[$page_id] = array(
				'topics' => bors_find_all('balancer_board_blog', $where),
				'no_show_answers' => true,
				'skip_message_footer' => true,
			);

		return $this->bdata[$page_id];
	}
}
