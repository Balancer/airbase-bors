<?php

class wrk_news_tags_json extends bors_json
{
	function data()
	{
		$view = bors_load('wrk_news_tags_view', $this->id());

		$news = [];

		foreach($view->items() as $x)
		{
			$content = $x->body();
			if($ah = $x->get('attaches_html'))
				$content .= "\n<br/>\n".$ah."<div style=\"clear:both\">&nbsp;</div>";

			$news[] = [
				'title' => $x->title(),
				'url' => $x->url_in_container(),
				'content' => $content,
			];
		}

		return ['news' => $news];
	}
}
