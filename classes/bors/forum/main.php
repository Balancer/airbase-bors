<?
	require_once('borsForumAbstract.php');
	class forum_main extends borsForumAbstract
	{
		function title() { return ec('форумы'); }
		function parents() { return array('http://balancer.ru/'); }
	}
