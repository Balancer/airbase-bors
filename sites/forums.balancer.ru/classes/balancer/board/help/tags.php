<?php

class balancer_board_help_tags extends balancer_board_page
{
	var $title = 'Описание тэгов';
	var $nav_name = 'тэги';
	var $is_auto_url_mapped_class = true;
	function template() { return 'forum/page.html'; }

	function body_data()
	{
//		$ch = new bors_cache();
//		if($ch->get('balancer_board_help_tags'))
//			return $ch->last();

		$tags = array();

		foreach(bors_dirs() as $dir)
		{
			foreach(search_dir($dir.'/classes', $mask='\.php$') as $file)
			{
				if(!preg_match('!(bors/)?lcml/tag/(pair|single)/!', $file, $mn))
					continue;

				$content = file_get_contents($file);
				if(!preg_match('!class (bors_)?lcml_tag_(pair|single)_(\w+)!', $content, $mt))
					continue;

				if(!preg_match('!^.*?/\*\*(.*?)\*/!s', $content, $md))
					continue;

				$type = "Тип тэга: ".($mt[2] == 'pair' ? 'парный' : 'одиночый');
				$desc = $md[1];
				$modify = bors_lib_time::short(filemtime($file));

				$tags[$mt[3]] = "<small>$type</small><br/>
<xmp>$desc</xmp>
<small>Дата модификации: $modify</small><br/>";
			}
		}
		ksort($tags);
//		var_dump($tags);
		return array(
			'tags' => $tags,
		);
	}
}
