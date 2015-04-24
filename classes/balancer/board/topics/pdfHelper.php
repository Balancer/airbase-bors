<?php

class balancer_board_topics_pdfHelper extends bors_page
{
	function auto_objects()
	{
		return array(
			'topic' => 'balancer_board_topic(id)',
		);
	}

	function can_be_empty() { return false; }
	function is_loaded() { return $this->topic() != NULL; }

	function template() { return "null.html"; }

	function body_data()
	{
		return array(
			'posts' => objects_array('balancer_board_post', array(
				'topic_id' => $this->id(),
				'order' => '`order`, `posted`',
			)),
		);
	}

	function pre_show()
	{
		template_noindex();
		return parent::pre_show();
	}

	function uri_name() { return 'tpdfhelper'; }
	function url_engine() { return 'url_titled'; }
	function base_url() { return $this->topic()->forum_id() && $this->topic()->forum() ? $this->topic()->forum()->category()->category_base_full() : '/'; }
	function modify_time() { return $this->topic()->modify_time(); }
}
