<?php

class balancer_board_post extends forum_post
{
	function extends_class() { return 'forum_post'; }

	function is_public_access() { return $this->topic() && $this->topic()->forum_id() && $this->topic()->forum()->is_public_access(); }

	function html($data = array())
	{
		$data = array_merge(array(
			'p' => $this,	//	сам постинг
			'forum' => $this->topic()->forum(), // форум, если нужно показать ссылку на него
//			'$no_show_answers = true, если не показывать ответы.
			'show_title' => true, // если нужна печать заголовка.
//$skip_author_name
//	$skip_avatar_block
//	$skip_date
//	$skip_forums - не отображать название форума
			'skip_votes' => true, //  - не рисовать иконки в заголовке
			'skip_message_footer' => true,
			'strip' => 1024, // - сколько резать символов.
		), $data);

		return bors_templates_smarty::render_data('xfile:/var/www/bors/bors-airbase/templates/forum/post.html', $data);
	}

	function text($data = array())
	{
		$data = array_merge(array(
			'p' => $this,	//	сам постинг
			'forum' => $this->topic()->forum(), // форум, если нужно показать ссылку на него
			'show_title' => true, // если нужна печать заголовка.
			'skip_votes' => true, //  - не рисовать иконки в заголовке
			'skip_message_footer' => true,
			'strip' => 1024, // - сколько резать символов.
		), $data);

		return bors_templates_smarty::render_data('xfile:/var/www/bors/bors-airbase/templates/forum/post.text', $data);
	}
}
