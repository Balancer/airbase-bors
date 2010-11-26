<?php

class_include('user_blog');

class user_blog_rss extends user_blog
{
	function render_engine() { return 'render_self'; }
	
	function url() { return parent::url(1)."rss.xml"; }

	function render()
	{
		include("3part/feedcreator.class.php"); 

		$rss = new UniversalFeedCreator(); 
		$rss->encoding = 'utf-8'; 
		$rss->title = $this->user->title().ec(": Блог");
		$rss->description = ec("Все темы, начатые пользователем  последние 30 дней. Не более 25 штук.").$this->user->title();
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
		foreach($this->db()->get_array('SELECT id FROM topics WHERE poster_id='.$this->id().' AND posted > '.(time()-30*86400).' ORDER BY posted DESC LIMIT 25') as $topic_id)
		{		
		    $item = new FeedItem();
			$topic = class_load('forum_topic', $topic_id);
	    	$item->title = $topic->title();
		    $item->link = $topic->url(); 
			
			$html = $topic->first_post()->body();
			if(strlen($html) > 1024)
			{
				include_once("funcs/texts.php");
				$html = strip_text($html, 1024);
				$html .= "<br /><br /><a href=\"".$topic->url(1).ec("\">Дальше »»»");
			}
			
			$item->description = $html;
			$item->date = $topic->create_time(); 
			$item->source = "http://balancer.ru/forum/";
			$item->author = $topic->owner()->title();
							     
			$rss->addItem($item); 
		} 
								
		$result = $rss->createFeed("RSS1.0");
		header("Content-Type: ".$rss->contentType."; charset=".$rss->encoding);
		return $result;
	}
	
	function cache_static() { return config('static_forum') ? 600 : 0; }
}
