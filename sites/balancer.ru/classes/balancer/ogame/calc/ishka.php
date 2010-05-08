<?php

class balancer_ogame_calc_ishka extends base_page
{
	function access() { return $this; }
	function can_action() { return true; }

	function on_action_calc($data)
	{
		list($target_name, $target_coords) = preg_extract($data['copy'], '/Тема:\s+Разведданные с (.*?) \[(\d+:\d+:\d+)\]/m', array(1,2));

		$date = preg_extract($data['copy'], '/Дата:\s+([\d\.: ]+)/m');
		$to = preg_extract($data['copy'], '/Кому:\s+(.+)/m');
		$from = preg_extract($data['copy'], '/.*От:\s+(.*)$/m');

		$metal = str_replace('.', '', preg_extract($data['copy'], '/Металл:\s+([\d\.]+)/'));
		$crystal = str_replace('.', '', preg_extract($data['copy'], '/Кристалл:\s+([\d\.]+)/'));
		$deuterium = str_replace('.', '', preg_extract($data['copy'], '/Дейтерий:\s+([\d\.]+)/'));

		$metal_mines = preg_extract($data['copy'], '/Рудник по добыче металла\s+(\d+)/');

		echo "Атака '$target_name' [$target_coords] ($date)<Br/>";
		echo "metal=$metal, crystal=$crystal, deuterium=$deuterium<Br/>";
		echo "мет шахт:=$metal_mines<Br/>";

		print_d($data);
		return true;
	}
}

function preg_extract(&$data, $mask, $group = 1, $default = NULL, $replace = true)
{
	if(preg_match($mask, $data, $m))
	{
		if($replace)
			$data = preg_replace($mask, '', $data);

		if(!is_array($group))
			return $m[$group];

		$result = array();

		foreach($group as $i)
			$result[] = $m[$i];

		return $result;
	}
	else
		return $default;
}
