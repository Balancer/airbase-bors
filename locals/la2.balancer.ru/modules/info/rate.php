<?
	echo module_local_info_rates();

	function module_local_info_rates()
	{
		foreach(array('main'=>7776, 'test'=>7778) as $srv => $port)
			foreach(array('xp', 'sp', 'adena', 'items', 'spoil') as $rate)
			{
				$var = $srv."_".$rate;
				$$var = @intval(@file_get_contents("http://la2.balancer.ru:$port/rates/$rate.fsh"));
			}

		echo <<< __EOT__
<table cellSpacing="0" class="btab" width="95%">
<tr><th>Rate</th><th>Основной</th><th>Тестовый</th></tr>
<tr><td>XP</td><td>x$main_xp</td><td>x$test_xp</td></tr>
<tr><td>SP</td><td>x$main_sp</td><td>x$test_sp</td></tr>
<tr><td>Adena</td><td>x$main_adena</td><td>x$test_adena</td></tr>
<tr><td>Items</td><td>x$main_items</td><td>x$test_items</td></tr>
<tr><td>Spoil</td><td>x$main_spoil</td><td>x$test_spoil</td></tr>
</table>
__EOT__;
	}
