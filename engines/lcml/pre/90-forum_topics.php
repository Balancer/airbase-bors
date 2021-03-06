<?php
	function lcml_forum_topics($txt)
	{
		if(!config('lcml_balancer'))
			return $txt;

		//http://forums.airbase.ru/index.php?showtopic=3353
		//http://forums.airbase.ru/index.php?act=ST&f=3&t=25525
		//http://www.airbase.ru/forums/index.php?act=ST&f=3&t=5830&st=105
		//http://forums.airbase.ru/index.php?showtopic=24667&st=345
		//http://forums.airbase.ru/index.php?act=ST&f=10&t=14646&view=findpost&p=154477

		$txt=preg_replace("!(\s|^)http://(www\.)?airbase\.ru/forums/!m","$1http://forums.airbase.ru/",$txt);
		$txt=preg_replace("!http://forums\.airbase\.ru/index\.php\?!","http://forums.airbase.ru/?",$txt);

		//		  http://forums.airbase.ru/?showtopic=22038&amp;st=30#entry385506
		//		  http://forums.airbase.ru/index.php?act=ST&amp;f=3&amp;t=22038&amp;st=30#entry385506
//		$txt=preg_replace("!(\s|^|\()http://forums\.airbase\.ru/\?act=ST&amp;showtopic=(\d+)&amp;st=(\d+)#entry(\d+)(\s|$|\.|,|\))!me","'$1'.lcml_forum_topics_post('$2','$4').'$5'",$txt);
		$txt=preg_replace_callback("!(\s|^|\()http://forums\.airbase\.ru/\?act=ST&amp;f=(\d+)&amp;t=(\d+)&amp;st=(\d+)#entry(\d+)(\s|$|\.|,|\))!m",
			function($m) { return $m[1].lcml_forum_topics_post($m[3],$m[5]).$m[6];}, $txt);

		$txt=preg_replace("!(showtopic=\d+)&amp;st=0!","$1",$txt);

//TODO:	Посмотреть, не нужна ли замена. Например: http://balancer.ru/g/p251869
//		$txt=preg_replace("!(\s|^|\()http://forums\.airbase\.ru/\?act=ST&amp;f=(\d+)&amp;t=(\d+)&amp;st=(\d+)(\s|$|\.|,|\))!me","'$1'.lcml_forum_topics_page('$2','$3','$4').'$5'",$txt);
//		$txt=preg_replace("!(\s|^|\()http://forums\.airbase\.ru/\?showtopic=(\d+)&amp;st=(\d+)(\s|$|\.|,|\))!me","'$1'.lcml_forum_topics_page('','$2','$3').'$4'",$txt);

		$txt=preg_replace("!http://forums\.airbase\.ru/\?act=ST&amp;f=\d+&amp;t=(\d+)!","http://forums.airbase.ru/?showtopic=$1",$txt);

		$txt=preg_replace_callback("!(\s|^|\()http://forums\.airbase\.ru/\?showtopic=(\d+)&amp;hl=(\s|$|\.|,|\))!m",
			function($m) { return $m[1].lcml_forum_topics_title($m[2]).$m[3];}, $txt);
		$txt=preg_replace_callback("!(\s|^|\()http://forums\.airbase\.ru/\?showtopic=(\d+)(\s|$|\.|,|\))!m",
			function($m) { return $m[1].lcml_forum_topics_title($m[2]).$m[3];}, $txt);
		$txt=preg_replace_callback("!(\s|^|\()http://forums\.airbase\.ru/\?showtopic=(\d+)&amp;view=findpost&amp;p=(\d+)(\s|$|\.|,|\))!m",
			function($m) { return $m[1].lcml_forum_topics_post($m[2],$m[3]).$m[4];}, $txt);
		$txt=preg_replace_callback("!(\s|^|\()http://balancer\.ru/.+viewtopic\.php\?id=(\d+)(\s|$|\.|,|\))!m",
			function($m) { return $m[1].lcml_forum_topics_title($m[2]).$m[3];}, $txt);
		$txt=preg_replace_callback("!(\s|^|\()http://balancer\.ru/.+viewtopic\.php\?pid=(\d+)#p(\d+)!m",
			function($m) { return $m[1].lcml_forum_post_title($m[2]);}, $txt);
		$txt=preg_replace_callback("!(\s|^|\()http://balancer\.ru/.+viewtopic\.php\?pid=(\d+)!m",
			function($m) { return $m[1].lcml_forum_post_title($m[2]);}, $txt);

		return $txt;
	}

	function lcml_forum_topics_title($topic_id)
	{
		$topic = class_load('balancer_board_topic', intval($topic_id));

		return $topic ? $topic->titled_link() : 'Unknown topic '.$topic_id;
	}

	function lcml_forum_post_title($post_id)
	{
		$post = class_load('forum_post', intval($post_id));

		return $post ? $post->titled_link() : 'Unknown posting '.$post_id;
	}

	function lcml_forum_topics_post($topic, $post)
	{
		$post = bors_load('balancer_board_post', $post);

		return "<a href=\"{$post->url_for_igo()}\">{$post->title()}</a>";
	}

	function lcml_forum_topics_page($forum,$topic,$start)
	{
		$dbh = @mysql_connect("localhost", "forum", "localforum") or die (__FILE__.':'.__LINE__." Could not connect");
		mysql_select_db("forums_airbase_ru") or die (__FILE__.':'.__LINE__." Could not select database");
		mysql_query ("SET CHARACTER SET utf8");

		if($forum)
			$url="http://www.airbase.ru/forums/index.php?act=ST&f=$forum&t=$topic&st=$start";
		else
			$url="http://forums.airbase.ru/index.php?showtopic=$topic&st=$start";
		$q="SELECT title FROM ib_topics WHERE tid=$topic";
		$query = mysql_query ($q) or  die(__FILE__.':'.__LINE__." Query '$q' failed, error ".mysql_errno().": ".mysql_error()."<BR>");
		$res = mysql_fetch_array($query);
		mysql_close();

		if($res['title'])
		{
			$title=chop($res['title']);
			if($start>0)
				$title.=" <font size=\"1\">(page ".(intval($start/15)+1).")</font>";
		}
		else
			$title=$url;
		return "<a href=\"$url\">$title</a>";
	}
