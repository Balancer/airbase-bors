<?
	function access_check($uri, $us = NULL)
	{
		if(!$us)
			$us = new User;

		if(!$us->data('id'))
		{
			$GLOBALS['page_data']['title'] = tr('Error');
			$GLOBALS['page_data']['source'] = tr('You are not logged in');

			show_page($uri);
			return false;
		}

		if(!access_allowed($uri))
		{
			$GLOBALS['page_data']['title'] = tr('Error');
			$GLOBALS['page_data']['source'] = tr('You have not permission to this action');

			show_page($uri);
			return false;
		}
		
		return true;
	}
?>
