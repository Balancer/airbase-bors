<?
	function cms_funcs_action_subscribe($page, $user=NULL)
	{
		if(!$user)
			$user = new User();

		$hts = new DataBaseHTS();
		
		$hts->append_data($user->get_page(), 'subscribe', $hts->normalize_uri($page));
	}

	function cms_funcs_action_unsubscribe($page, $user=NULL)
	{
		if(!$user)
			$user = new User();

		$hts = new DataBaseHTS();

//		$GLOBALS['log_level'] = 9;	
		$hts->remove_data($user->get_page(), 'subscribe', $hts->normalize_uri($page));
//		exit("qqq");
	}

	function cms_funcs_action_is_subscribed($page, $user=NULL)
	{
		if(!$user)
			$user = new User();

		$hts = new DataBaseHTS();
		
		return $hts->data_exists($user->get_page(), 'subscribe', $hts->normalize_uri($page));
	}

	function cms_funcs_action_get_all_subscribed($user=NULL)
	{
		if(!$user)
			$user = new User();

		$hts = new DataBaseHTS();
		
		return $hts->get_data_array($user->get_page(), 'subscribe');
	}
?>
