<?php

class wrk_main extends bors_page
{
	function title() { return ec('W.R.K.: Сообщество Balancer.Ru'); }
	function nav_name() { return ec('W.R.K.'); }

	function config_class() { return 'wrk_config'; }
}
