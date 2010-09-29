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
		$stars_count = round(abs($reputation)/10)/2;

		if(!$stars_count)
			return '';

//		☆★
		$stars = str_repeat('★', $full_stars = intval($stars_count));
		if($full_stars != $stars_count)
			$stars .= '☆';

		if(!$stars)
			return '';

		if($reputation >= 0)
			return "<div style=\"color: gold; font-size: 30px; font-family: monospace\">{$stars}</div>";
		else
			return "<div class=\"rot180\" style=\"color: gray; font-size: 30px; font-family: monospace\">{$stars}</div>";
	}
}
