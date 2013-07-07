<?php

class balancer_board_topics_printableCurrent extends balancer_board_paged
{
	function auto_objects()
	{
		return array(
			'topic' => 'balancer_board_topic(id)',
		);
	}

	function can_be_empty() { return false; }
	function is_loaded() { return $this->topic() != NULL; }
	function title() { return $this->topic()->title().', страница '.max(1, $this->page()); }
	function nav_name() { return 'версия для печати страницы '.max(1, $this->page()); }
	function parents() { return array($this->topic()->url_ex($this->page())); }

	function template() { return "forum/printable.html"; }

	function main_class() { return 'balancer_board_post'; }
	function where()
	{
		return array(
			'topic_id' => $this->id(),
		); 
	}

	function order() { return '`order`, `posted`'; }

	function item_per_page() { return $this->topic()->item_per_page(); }
	function items_around_page() { return 30; }

	function pre_show()
	{
		template_noindex();
		return parent::pre_show();
	}

	function uri_name() { return 'tpc'; }
	function url_engine() { return 'url_titled'; }
	function base_url() { return $this->topic()->forum_id() && $this->topic()->forum() ? $this->topic()->forum()->category()->category_base_full() : '/'; }
	function modify_time() { return $this->topic()->modify_time(); }
}
