<?php

function lst_forum($txt)
{
	if(!trim($txt))
		return "";

	if($ubb = bors_load('forum_topic_ubb', "14/$txt"))
		$tid = $ubb->topic_id();
	else
		$tid = $txt;

	if($tid)
		$topic = bors_load('balancer_board_topic', $tid);
	else
		return "";
	
	if(!$topic)
		return "";

	return "<script src=\"/js/board/comments/$tid.js\"></script>".
		"<noscript><a href=\"{$topic->url()}\">комментарии ({$topic->num_replies()})</a></noscript>";
}
