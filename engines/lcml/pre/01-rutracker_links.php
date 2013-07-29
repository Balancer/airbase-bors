<?php

function lcml_rutracker_links($text, $lcml = NULL)
{
//	if(config('is_developer'))
//	{
		if($lcml && ($p = $lcml->p('self')) && ($t = $p->get('topic')) && $t->forum_id() == 73)
			return $text;

		$text = preg_replace("!https?://(www\.)?rutracker.org/[^\s\"]+!", '[red]Ссылка запрещена по требованию Google[/red]', $text);
		$text = preg_replace("!https?://(www\.)?torrents.ru/[^\s\"]+!", '[red]Ссылка запрещена по требованию Google[/red]', $text);
		$text = preg_replace("!https?://(www\.)?rapidshare\.(com|ru)/[^\s\"]+!", '[red]Ссылка запрещена по требованию Google[/red]', $text);
		$text = preg_replace("!https?://(www\.)?depositfiles.com/[^\s\"]+!", '[red]Ссылка запрещена по требованию Google[/red]', $text);
		$text = preg_replace("!https?://(www\.)?ifile.it/[^\s\"]+!", '[red]Ссылка запрещена по требованию Google[/red]', $text);
//	}

	return $text;
}
