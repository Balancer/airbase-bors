<?php

require_once('inc/lists.php');

class module_forum_lenta_public extends base_page
{
	function main_db_storage(){ return 'punbb'; }

	private $bdata = array();
	function local_template_data_set()
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
			$where['forum_id IN'] = parse_condensed_list($forums);

		$this->bdata[$page_id] = array(
				'topics' => objects_array('forum_blog', $where),
				'no_show_answers' => true,
				'skip_message_footer' => true,
			);

		return $this->bdata[$page_id];
	}
}
