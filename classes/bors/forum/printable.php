<?php

class forum_printable extends balancer_board_topic
{
	function uri_name() { return 'printable'; }

    function parents() { return array("balancer_board_topic://".$this->id()); }
    function nav_name() { return ec('Версия для печати'); }

	function body()
	{
		$forum = class_load('forum_forum', $this->forum_id());

		if(!$forum->can_read())
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");

		$GLOBALS['cms']['cache_disabled'] = true;

		include_once("engines/smarty/assign.php");
		$data = array();

		$query = "SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY id";

		$posts = $this->db()->get_array($query);

		$data['posts'] = array();

		foreach($posts as $pid)
			$data['posts'][] = class_load('forum_post', $pid);

		return template_assign_data("templates/printable.html", $data);
	}

	function template()
	{
		return "forum/printable.html";
	}
}
