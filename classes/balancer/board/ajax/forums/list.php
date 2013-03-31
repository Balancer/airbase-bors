<?php

class balancer_board_ajax_forums_list extends bors_pages_pure
{
	var $auto_map = true;

	function category_tree($parent_category = NULL, $step = 0)
	{
		$t = str_repeat("\t", $step);
		$result = array();

		if($parent_category)
		{
			foreach(bors_find_all('balancer_board_category', array('parent' => $parent_category->id())) as $cat)
			{
				if($sub = self::category_tree($cat, $step+1))
				{
					$result[] = "$t<li class=\"dropdown-submenu\"><a href=\"{$cat->url()}\">{$cat->title()}</a>";
					$result[] = "$t<ul class=\"dropdown-menu\">\n".$sub."\n$t</ul>\n$t</li>";
				}
				else
					$result[] = "$t<li><a href=\"{$cat->url()}\">{$cat->title()}</a></li>";
			}

			if($result)
				$result[] = "$t<li class=\"divider\"></li>";

			foreach(bors_find_all('balancer_board_forum', array('category_id' => $parent_category->id(), 'parent IS NULL')) as $f)
			{
				if($sub = self::forum_tree($f, $step+1))
				{
					$result[] = "$t<li class=\"dropdown-submenu\"><a href=\"{$f->url()}\">{$f->title()}</a>";
					$result[] = "$t<ul class=\"dropdown-menu\">\n".$sub."\n$t</ul>";
				}
				else
					$result[] = "$t<li><a href=\"{$f->url()}\">{$f->title()}</a></li>";
			}
		}
		else
		{
			foreach(bors_find_all('balancer_board_category', array('parent IS NULL')) as $cat)
			{
				if($sub = self::category_tree($cat, $step+1))
				{
					$result[] = "$t<li class=\"dropdown-submenu\"><a href=\"{$cat->url()}\">{$cat->title()}</a>";
					$result[] = "$t<ul class=\"dropdown-menu\">\n".$sub."\n$t</ul>\n$t</li>";
				}
				else
					$result[] = "$t<li><a href=\"{$cat->url()}\">{$cat->title()}</a></li>";
			}
		}

		return join("\n", $result);
	}

	function forum_tree($parent_forum = NULL, $step)
	{
		$result = array();
		$t = str_repeat("\t", $step);

		if($parent_forum)
		{
			foreach(bors_find_all('balancer_board_forum', array('parent_forum_id' => $parent_forum->id())) as $f)
			{
				if($sub = self::forum_tree($f, $step+1))
				{
					$result[] = "$t<li class=\"dropdown-submenu\"><a href=\"{$f->url()}\">{$f->title()}</a>";
					$result[] = "$t<ul class=\"dropdown-menu\">\n".$sub."\n$t</ul>\n$t</li>";
				}
				else
					$result[] = "$t<li><a href=\"{$f->url()}\">{$f->title()}</a></li>";
			}
		}
		else
		{
			foreach(bors_find_all('balancer_board_forum', array('parent_forum_id IS NULL')) as $f)
			{
				if($sub = self::forum_tree($f, $step+1))
				{
					$result[] = "$t<li class=\"dropdown-submenu\"><a href=\"{$f->url()}\">{$f->title()}</a>";
					$result[] = "$t<ul class=\"dropdown-menu\">\n".$sub."\n$t</ul>\n$t</li>";
				}
				else
					$result[] = "$t<li><a href=\"{$f->url()}\">{$f->title()}</a></li>";
			}
		}

		return join("\n", $result);
	}

	function body()
	{
		$body_cache = new Cache();
		if($body_cache->get('bootstrap-ajaxs', 'forums-tree'))
			return $this->attr['body'] = $body_cache->last();

		$tree = self::category_tree();
		$tree = "<ul class=\"dropdown-menu\">\n{$tree}\n</ul>\n";
		return $body_cache->set($tree, 3600);
	}
}
