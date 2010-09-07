<?php

class forum_tools_post_do extends base_page
{
	function main_db() { return config('punbb.database', 'punbb'); }

	function can_cache() { return false; }

	function pre_show()
	{
		$p = object_load('forum_post', intval($this->id()));
		$t = $p->topic();

		if(!bors()->user() || !bors()->user()->group()->is_coordinator())
			return bors_message(ec('У Вас нет прав для выполнения этой операции'));

		switch($this->page())
		{
			case 'drop-cache':
				$p->set_post_body(NULL, true);
				$p->cache_clean();
				$p->store();
				$t->cache_clean();
				$t->set_modify_time(time(), true);
				$t->store();
				break;
			default:
				break;
		}

		return go($p->url());
	}
}
