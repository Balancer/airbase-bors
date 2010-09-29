<?php

class balancer_board_user extends forum_user
{
	function extends_class() { return 'forum_user'; }

	/**
		Расчёт репутации в диапазоне -100 .. 100
	*/

	function reputation_percents()
	{
		$reputation_value = $this->reputation(); // абсолютное значение репутации, от -∞ до +∞

		// Репутация в диапазоне -100..100
		$rep = abs(200*atan($reputation_value*$reputation_value/($reputation_value >= 0 ? 300 : 100))/pi());
		if($reputation_value < 0)
			$rep = -$rep;
		return $rep;
	}

	function reputation_html()
	{
//		return "<img src=\"http://balancer.ru/user/{$this->id()}/rep.gif\" class=\"rep\" alt=\"\" />";

		$reputation = $this->reputation_percents();

		// Нормируем в диапазоне [0,5] с шагом 0,5
		$stars_count = 0.5+round(abs($reputation)*9.5/100)/2;

		if(!$stars_count)
			return '';

//		☆★
		$stars = str_repeat('★', $full_stars = intval($stars_count));
		if($full_stars != $stars_count)
			$stars .= '☆';

		if(!$stars)
			return '';

		if($reputation >= 0)
			return "<div class=\"rep\" style=\"color: gold; font-size: 16pt; font-family: sans-serif\">{$stars}</div>";
		else
			return "<div class=\"rep rot180\" style=\"color: gray; font-size: 16pt; font-family: sans-serif\">{$stars}</div>";
	}

	function warnings_html()
	{
		$warnings = $this->warnings();

		if(is_object($this->is_banned()))
			return "<div style=\"color: red; font-size: 7pt; font-family: sans-serif\">админ. бан</div>";

		if(!$warnings)
			return '';

		if($warnings >= 10)
		{
			$db = new driver_mysql('punbb');
			$total = 0;
			$time  = 0;
			foreach($db->get_array("SELECT score, time FROM warnings WHERE user_id = {$this->id()} ORDER BY time DESC LIMIT 20") as $w)
			{
				$total += $w['score'];
				if($total >= 10)
				{
					$time = $w['time'];
					break;
				}
			}

			return "<div style=\"color: red; font-size: 7pt; font-family: sans-serif\">бан до ".strftime("%d.%m.%Y", $this->expired = $time+WARNING_DAYS*86400)."</div>";
		}

//		☠
		$skulls = str_repeat('☠', $full_skulls = intval($warnings/2));
		if($full_skulls*2 != $warnings)
			$skulls .= '<span style="color:#999">☠</span>';

		if(!$skulls)
			return '';

		return "<div class=\"warn\" style=\"margin:0; padding:0; color: black; font-size: 15pt; font-family: sans-serif\">{$skulls}</div>";
	}
}
