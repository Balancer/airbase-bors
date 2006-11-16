<?
	function cms_funcs_action_favorites_user_page($user=NULL)
	{
		if(!$user)
			$user = new User();
			
		return "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/favorites/user".$user->data('id')."/";
	}

	function cms_funcs_action_is_favorite($uri, $user=NULL)
	{
		if(!$user)
			$user = new User();

		$hts = new DataBaseHTS();
		$favor = cms_funcs_action_favorites_user_page($user);
		
		return $hts->data_exists($favor, 'child', $uri);
	}
