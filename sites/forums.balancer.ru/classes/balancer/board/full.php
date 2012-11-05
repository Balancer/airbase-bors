<?php

// http://www.reaa.ru/cgi-bin/yabb/YaBB.pl
// http://paralay.iboards.ru/viewtopic.php?style=12&f=5&t=2332
// via http://www.balancer.ru/g/p2971364

class balancer_board_full extends balancer_board_page
{
	function title() { return 'Все форумы Balancer.ru'; }
	function nav_name() { return 'все'; }
	function auto_map() { return true; }
	function template() { return 'xfile:forum/wide.html'; }

	function body()
	{
		$me_group = balancer_board_user::me_group();
		$ch = new bors_cache;
		if(!($body = $ch->get('balancer_board_full_body', 'v2-group-'.$me_group->id().'-'.$this->modify_time())))
		{
			$body = parent::body();
			$ch->set($body, rand(120, 300));
		}
//		else
//			$body = 'cached['.date('r', $this->modify_time()).']:'.$body;

		return $body;
	}

	function body_data()
	{
		$me_group = balancer_board_user::me_group();

		$ch = new bors_cache;
		if(!($forums_cache = $ch->get('balancer_board_all_forums', 'v2-group-'.$me_group->id())))
		{
			$categories = array();
			$forums = array();

			foreach(bors_find('balancer_board_forum')
						->inner_join('balancer_board_category', 'balancer_board_category.id = balancer_board_forum.category_id')
						->order('balancer_board_category.sort_order, balancer_board_forum.sort_order')
					->all() as $f)
			{
				if($f->can_read_by_group($me_group))
				{
					$cat_id = $f->category_id();
					if(empty($categories[$cat_id]))
						$categories[$cat_id] = $f->category();

					if($f->parent_forum_id())
						$forums[$cat_id][$f->parent_forum_id()]['children'][] = $f;
					else
						$forums[$cat_id][$f->id()]['self'] = $f;
				}
			}

			$ch->set(compact('forums', 'categories'), rand(600, 900));
		}
		else
			extract($forums_cache);

		return array_merge(parent::body_data(), array(
			'forums' => $forums,
			'categories' => $categories,
		));
	}

	function modify_time() { return max(filemtime($this->class_file()), filemtime($this->body_template())); }
}
