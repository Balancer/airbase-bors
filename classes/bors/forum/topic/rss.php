<?php

class forum_topic_rss extends forum_topic
{
	function render_engine() { return 'self'; }
	
	function url() { return $this->rss_url(); }
//	function use_temporary_static_file() { return false; }
	
	function render($object)
	{
		$topic = object_load('forum_topic', $object->id());
		$forum = object_load('forum_forum', $topic->forum_id());
		
		if(!$forum->can_read())
			return ec("Извините, доступ к этому ресурсу закрыт для Вас");

		require_once("feedcreator.class.php"); 

		$rss = new UniversalFeedCreator(); 
		$rss->encoding = 'utf-8'; 
		$rss->title = $object->title();
		$rss->description = ec("Ответы в топик ").$object->title();
		$rss->link = parent::url(1);
		$rss->syndicationURL = $object->url(); 

/*		$image = new FeedImage(); 
		$image->title = "dailyphp.net logo"; 
		$image->url = "http://www.dailyphp.net/images/logo.gif"; 
		$image->link = "http://www.dailyphp.net"; 
		$image->description = "Feed provided by dailyphp.net. Click to visit."; 
		$rss->image = $image; 
*/
		// get your news items from somewhere, e.g. your database: 
		foreach($object->db()->get_array("SELECT id FROM posts WHERE topic_id={$object->id()} ORDER BY posted DESC LIMIT 50") as $post_id)
		{
		    $item = new FeedItem();
			$post = class_load('balancer_board_post', $post_id);
	    	$item->title = $object->title();
		    $item->link = $post->url_in_container();

			$html = $post->body();
			if(strlen($html) > 1024)
			{
				include_once("inc/texts.php");
				$html = strip_text($html, 1024);
				$html .= "<br /><br /><a href=\"".$post->url_in_container().ec("\">Дальше »»»");
			}

			$item->description = $html;
			$item->date = $post->create_time(); 
			$item->source = "http://balancer.ru/forum/";
			if($post->owner())
				$item->author = $post->owner()->title();
			else
				debug_hidden_log('lost-data', 'Unknown author for '.$post);

			$rss->addItem($item);
		} 

		$result = $rss->createFeed("RSS1.0");
		header("Content-Type: ".$rss->contentType."; charset=".$rss->encoding);
		return $result;
	}

	function cache_groups() { return parent::cache_groups()." airbase-forum-topic-".$this->id(); }
}
