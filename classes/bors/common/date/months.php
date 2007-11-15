<?
class common_date_months
{
	function id() { return 0; }
	function page() { return 1; }

	function months_list_rp()
	{
		return array(
			0 => '-----',
			1 => ec('Января'),
			2 => ec('Февраля'),
			3 => ec('Марта'),
			4 => ec('Апреля'),
			5 => ec('Мая'),
			6 => ec('Июня'),
			7 => ec('Июля'),
			8 => ec('Августа'),
			9 => ec('Сентября'),
			10 => ec('Октября'),
			11 => ec('Ноября'),
			12 => ec('Декабря'),
		);
	}
}
