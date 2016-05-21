<?php

namespace Airbase\Modules;

class Anniversary
{
	static function html()
	{
		$file = __DIR__.'/../../data/anniversary/'.date('md').'.txt';
		if(!file_exists($file))
			return '';

		$data = explode("\n", trim(file_get_contents($file)));

		array_walk($data, function(&$s) {
			list($year, $title, $image, $url) = preg_split('/\s*\|\s*/', $s);
			$s = [
				'year' => trim($year),
				'title' => trim($title),
				'image' => trim($image),
				'url' => trim($url),
				'diff' => date('Y') - $year,
			];

			if(empty($image))
				return $s = NULL;

			if($year == '*')
				return $s;

			if(preg_match('/^(\d+)\*$/', $year, $m))
			{
				$s['year'] = $m[1];
				return $s;
			}

			if(preg_match('/^(\d+)\!$/', $year, $m) && $m[1] == date('Y'))
			{
				$s['year'] = $m[1];
				return $s;
			}

			if(!is_numeric($year))
				return $s = NULL;

			if($year <= date('Y'))
				return $s;

			$diff = date('Y') - $year;
			if($diff<=5)
				return $s;

			if($diff>5 and $diff % 5 == 0)
				return $s;

			$s = NULL;
		});

		$data = array_filter($data);

		if(empty($data))
			return false;

		$x = $data[rand(0, count($data)-1)];

		extract($x);

		$info = getimagesize($image);

		$s_year = intval($year);
		if($diff%10 == 0)
			$s_year = "<span class=\"b red\">{$year}</span>";

		if($s_year != '*' && $year && $diff)
			$desc = "<div class=\"small center\">{$s_year}: {$title} (".sklonn($diff,'год,года,лет').").</div>";
		else
			$desc = "<div class=\"small center\">{$title}</div>";

		$html = "
<dl class=\"box w200\">
	<dd>
		<a href=\"$url\"><img src=\"$image\" title=\"$title\" {$info[3]} /></a>
		$desc
	</dd>
</dl>
";

		return $html;
	}
}
