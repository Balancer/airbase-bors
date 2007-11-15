<?
class common_date_days
{
	function id() { return 0; }
	function page() { return 1; }

	function days_list()
	{
		$res = array(0=>'--');

		for($d=1; $d<32; $d++)
			$res[$d] = $d;

		return $res;
	}

	function days_list_cur()
	{
		$res = array(0=>'--');

		for($d=1; $d<32; $d++)
			$res[$d] = $d;

		return $res;
	}
}