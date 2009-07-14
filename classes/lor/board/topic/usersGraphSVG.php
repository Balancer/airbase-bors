<?php

class lor_board_topic_usersGraphSVG extends base_image_svg
{
	function image()
	{
		debug_hidden_log('001', 'check for lor_board_topic_usersGraphSVG');

		require_once('inc/http.php');

		$url = "http://www.linux.org.ru/view-message.jsp?msgid={$this->id()}&page=-1";
		$topic = http_get($url);
		if(preg_match('!<title>(.*)</title>!', $topic, $m))
			$title = $m[1];
		else
			$title = 'Linux.Org.Ru';

//		echo "<xmp>"; print_d($topic); echo "</xmp>"; exit();

//		$topic = preg_replace("")

		$posts = array_filter(preg_split("/<!\-\-.*?\-\->/", $topic), create_function('$s', 'return preg_match("/<a href=.whois.jsp.nick=/", $s);'));

//		echo "<xmp>"; print_d($posts); echo "</xmp>"; exit();

		$users = array();
		$edges = array();
		$max = 1;
		$maxu = 1;
		foreach($posts as $s)
		{
			$s = trim(urldecode($s));
			$user = '';
			$answ = '';
			
//			if(preg_match('!<div class=msg id="comment\-\d+">.*Ответ на:.*\d+#comment\-\d+.*</a> от (.+?) \d+\.\d+\.\d+ .*<div class=sign>.+? <img src=".*="whois\.jsp\?nick=(.+?)">!s', $s, $m))
//			if(preg_match('!.*?Ответ на: <a href="view\-message\.jsp?msgid=.*?</a> от (.+?) \d+\.\d+.\d+ \d+:\d+:\d+&nbsp;</div><div class="msg_body">.*<h2>Re: Новый раздел Google?</h2><i>&gt;Опенсурс - в опенсурс, проприетарные поделки гугла - в проприетаное ПО, нет?</i><br>'
			if(preg_match('!Ответ на:.*\d+#comment\-\d+.*</a> от (.+?) \d+\.\d+.\d+ \d+:\d+:\d+&nbsp;.*<a href="whois\.jsp\?nick=(.+?)">!s', $s, $m))
			{
				$user = $m[2];
				$answ = $m[1];
			}
//			elseif(preg_match('!<div class=title>\[<a href="/jump\-message\.jsp\?msgid=\+d&amp;cid=\d+">#</a>\]&nbsp;.*<div class=sign>.+? <img src=".*="whois\.jsp\?nick=(.+?)">!s', $s, $m))
			elseif(preg_match('!<a href="whois\.jsp\?nick=(.+?)">!s', $s, $m))
			{
				$user = $m[1];
			}
			else
				debug_hidden_log('lor', "Unknown string '$s'");

			if(empty($users[$user]))
				$users[$user] = array(
					'name' => $user,
					'link' => "http://www.linux.org.ru/whois.jsp?nick=".urlencode($user),
					'count' => 1,
				);
			else
			{
				$cnt = ++$users[$user]['count'];
				if($cnt > $maxu)
					$maxu = $cnt;
			}

			if($answ)
			{
				$from = $user;
				$to = $answ;
				
				if(!$this->args('ordered'))
					if($from < $to)
						list($to, $from) = array($from, $to);
				
				if(empty($edges[$from][$to]))
					$edges[$from][$to] = array(
						'count' => 1,
					);
				else
				{
					$cnt = ++$edges[$from][$to]['count'];
					if($cnt > $max)
						$max = $cnt;
				}
			}
		}

		$title = "Граф взаимных ответов участников темы «{$title}»";

		require_once 'Image/GraphViz.php';
		$graph = new Image_GraphViz();
		$graph->setAttributes(array(
			'label' => $title,
			'labelloc' => 't',
			'URL' => $url,
		));
		
		foreach($users as $uid => $ud)
			$graph->addNode(
				$uid,
					array(
						'URL'   => $ud['link'],
						'label' => $ud['name'],
//						'tooltip' => $ud['reputation'],
//						'shape' => 'box',
//				 		'fontsize' => 8+intval(12*$ud['count']/$maxu),
//						'fillcolor' => $ud['reputation'] >= 0 ? 
					)
			);

		foreach($edges as $from_id => $to_ids)
		{
			foreach($to_ids as $to_id => $x)
			{
				$graph->addEdge(
					array(
						$from_id => $to_id,
					),

					array(
						'label' => $x['count'],
						'arrowhead' => $this->args('ordered') ? 'normal' : 'none',
						'penwidth' => pow($x['count']/$max, 0.25)*4,
//						'style' => 'dashed',
						'color' => sprintf('#%2x%2x%2x', rand(0,128), rand(0,128), rand(0,128)),
					)
				);
			}
		}
		
		ob_start();
		$graph->image('svg');
		$svg = ob_get_contents();
		ob_end_clean();
		
		return $svg;
	}	

	function cache_static() { return rand(3600, 7200); }
}
