<?php

class balancer_board_smilies extends balancer_board_page
{
	var $title = 'Смайлики форумов';
	var $nav_name = 'смайлики';

	function body_data()
	{
		require_once 'engines/lcml/post/00-smilies.php';

		$smilies = [];
		$files = [];

		$smilies_list = explode("\n", file_get_contents(config('smilies_dir')."/list.txt"));

		foreach($smilies_list as $s)
		{
			if(preg_match('/^(\S+)\s*(\w+)$/', $s, $m))
			{
				$smilies[$m[2]][] = $m[1];
				foreach(['gif', 'png', 'jpg'] as $ext)
				{
					if(file_exists(config('smilies_dir')."/{$m[2]}.{$ext}"))
						$files[$m[2]] = config('smilies_url')."/{$m[2]}.{$ext}";
				}
			}
		}

        foreach(lcml_smilies_load(config('smilies_dir')) as $code => $ext)
        {
        	$smilies[$code][] = ":$code:";
			$files[$code] = config('smilies_url')."/{$code}.{$ext}";
        }

		return [
			'smilies' => $smilies,
			'files' => $files,
		];
	}
}
