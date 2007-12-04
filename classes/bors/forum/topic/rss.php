<?php

class_include('forum_topic');

class forum_topic_rss extends forum_topic
{
	function render_engine() { return 'render_self'; }
	
	function url() { return $this->rss_url(); }

	function render()
	{
		$topic = object_load('forum_topic', $this->id());
		$forum = object_load('forum_forum', $topic->forum_id());
		
		if(!$forum->can_read())
			return ec("Извините, доступ к этому ресурсу закрыт для Вас");

		include("3part/feedcreator.class.php"); 

		$rss = &new UniversalFeedCreator(); 
		$rss->encoding = 'utf-8'; 
		$rss->title = $this->title();
		$rss->description = ec("Ответы в топик ").$this->title();
		$rss->link = parent::url(1);
		$rss->syndicationURL = $this->url(); 

/*		$image = new FeedImage(); 
		$image->title = "dailyphp.net logo"; 
		$image->url = "http://www.dailyphp.net/images/logo.gif"; 
		$image->link = "http://www.dailyphp.net"; 
		$image->description = "Feed provided by dailyphp.net. Click to visit."; 
		$rss->image = $image; 
*/
		// get your news items from somewhere, e.g. your database: 
		$db = &new DataBase('punbb');
		foreach($db->get_array("SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY posted DESC LIMIT 50") as $post_id)
		{		
		    $item = &new FeedItem();
			$post = class_load('forum_post', $post_id);
	    	$item->title = $this->title();
		    $item->link = $post->url(); 
			
			$html = $post->body();
			if(strlen($html) > 1024)
			{
				include_once("funcs/texts.php");
				$html = strip_text($html, 1024);
				$html .= "<br /><br /><a href=\"".$post->url(1).ec("\">Дальше »»»");
			}
			
			$item->description = $html;
			$item->date = $post->create_time(); 
			$item->source = "http://balancer.ru/forum/";
			$item->author = $post->owner()->title();
							     
			$rss->addItem($item); 
		} 
								
		$result = $rss->createFeed("RSS1.0");
		header("Content-Type: ".$rss->contentType."; charset=".$rss->encoding);
		return $result;
	}
	
	function cache_static() { return 600; }
}
