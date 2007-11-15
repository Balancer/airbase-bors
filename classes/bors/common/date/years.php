<?
class common_date_years
{
	function id() { return 0; }
	function page() { return 1; }

	function years_list()
	{
		$res = array( 0 => '----');

		for($d = strftime('%Y', time())+1; $d >= 1900; $d--)
			$res[$d] = $d;

		return $res;
	}
}
