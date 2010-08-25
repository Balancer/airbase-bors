<?php

class forum_tools_topic extends base_page
{
	function can_be_empty() { return true; }

	function parents() { return array('forum_topic://'.$this->id()); }
	function topic() { return object_load('forum_topic', $this->id()); }

	function title() { 	return ec('Операции над темой'); }
	function template()
	{
		return 'forum/common.html';
	}

	function access() { return $this; }
	function can_read() { template_noindex(); return true; }
	function can_action()
	{
		$me = bors()->user();
        if(!$me || !$me->id())
            return false;

		if(!$me->group()->can_move())
			return false;

		return true;
	}

	function on_action_topic_edit($data)
	{
		foreach(explode(' ', 'description keywords_string title') as $key)
			$this->topic()->{"set_$key"}($data[$key], true);

		$this->topic()->cache_clean();
		return go($this->topic()->url());
	}

	function local_data()
	{
		$db = new driver_mysql(config('punbb.database', 'punbb'));

		return array(
			'me' => bors()->user(),
			'is_subscribed' => $dbh->select('subscriptions', 'COUNT(*)', array('user_id' => bors()->user_id(), 'topic_id' => $this->id())),
		);
	}
}

