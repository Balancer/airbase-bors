<?php

class airbase_forum_tags extends balancer_board_page
{
	function config_class() { return 'airbase_forum_config'; }
	function render_engine() { return 'render_php'; }
	function template() { return 'airbase/forum/main.tpl.php'; }

	function title() { return ec('тест ключевых слов'); }
}
