<?php

class forum_printable extends balancer_board_topic
{
	function uri_name() { return 'printable'; }

    function parents() { return array("balancer_board_topic://".$this->id()); }
    function nav_name() { return ec('Версия для печати'); }

	function items_per_page() { return 100; }

	function total_items() { return balancer_board_post::find(['topic_id' => $this->id()])->count(); }

	function body()
	{
		$forum = class_load('forum_forum', $this->forum_id());

		if(!$forum->can_read())
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");

		$GLOBALS['cms']['cache_disabled'] = true;

		include_once("engines/smarty/assign.php");
		$data = array();

		$data['posts'] = balancer_board_post::find(['topic_id' => $this->id()])->order('create_time')->all($this->page(), $this->items_per_page());

		return template_assign_data("templates/printable.html", $data);
	}

	function template()
	{
		return "forum/printable.html";
	}
}
