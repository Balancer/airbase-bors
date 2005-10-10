<?
    register_action_handler('topic-show', 'handler_topic_show');

    function handler_topic_show($uri, $action)
	{
		require_once('inc/access_check.php');

		if(!access_check($uri))
			return true;

		$hts = new DataBaseHTS();
		$parent = $hts->get_data('parent', $uri);
		$hts->remove_data($uri, 'flags', 'hidden');
		
		go($parent);

		return true;
	}
?>
