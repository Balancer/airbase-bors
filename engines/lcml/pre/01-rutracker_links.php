<?php

function lcml_rutracker_links($text, $lcml = NULL)
{
	if(config('is_developer'))
	{
		if($lcml && ($p = $lcml->p('self')) && ($t = $p->get('topic')) && $t->forum_id() == 73)
			return $text;

		$text = preg_replace("!http://rutracker.org/[^\s\"]+!", '[red]Ссылка запрещена по требованию Google[/red]', $text);
	}

	return $text;
}
