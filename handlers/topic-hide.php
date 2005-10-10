<?
    register_action_handler('topic-hide', 'handler_topic_hide');

    function handler_topic_hide($uri, $action)
	{
		$us = new User;

		if(!$us->data('id'))
		{
			$GLOBALS['page_data']['title'] = tr('Error');
			$GLOBALS['page_data']['source'] = tr('You are not logged in');

			show_page($uri);
			return true;
		}

		if(!access_allowed($uri))
		{
			$GLOBALS['page_data']['title'] = tr('Error');
			$GLOBALS['page_data']['source'] = tr('You have not permission to this action');

			show_page($uri);
			return true;
		}

		$hts = new DataBaseHTS();
		$parent = $hts->get_data('parent', $uri);
		$hts->append_data($uri, 'flags', 'hidden');
		
		go($parent);

		return true;
	}
?>
