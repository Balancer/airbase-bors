<?php

class balancer_board_help_tags extends balancer_board_page
{
	var $title = 'Описание тегов';
	var $nav_name = 'теги';
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

				$type = "Тип тега: ".($mt[2] == 'pair' ? 'парный' : 'одиночый');
				$desc = $md[1];
				$sample = "";
				if(preg_match("/\n\s+Ссылка с примером: (.+?)\n/", $desc, $msampl))
				{
					$desc = preg_replace("/\n\s+Ссылка с примером: (.+?)\n/", "\n", $desc);
					if($example_object = bors_load_uri($msampl[1]))
						$sample = "<i>Ссылка с примером: ".$example_object->titled_link()."</i><br/>";
				}

				$modify = bors_lib_time::short(filemtime($file));

				$tags[$mt[3]] = "<small>$type</small><br/>
<xmp>$desc</xmp>
$sample
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
