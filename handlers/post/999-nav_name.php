<?php
	hts_data_posthandler_add("!.*!", 'nav_name', 'bors_none_nav_name');
	function bors_none_nav_name($uri, $m)
	{	
		$hts = &new DataBaseHTS($uri);
		return $hts->get('title');
	}
