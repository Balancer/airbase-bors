<?php

class forum_tools_topic extends balancer_board_page
{
	function parents() { return array('balancer_board_topic://'.$this->id()); }
	function topic() { return bors_load('balancer_board_topic', $this->id()); }

	function title() { 	return ec('Операции над темой ').$this->topic()->title(); }
	function nav_name() { return ec('операции над темой'); }

	function template() { return 'xfile:forum/common.html'; }

	function pre_show()
	{
		template_noindex();
		return parent::pre_show();
	}

	function access() { return $this; }
	function can_read() { return true; }
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

	function body_data()
	{
		$db = new driver_mysql(config('punbb.database', 'punbb'));
		$user_ids = $db->select_array('posts', 'DISTINCT poster_id', array(
			'topic_id' => $this->id(),
		));

		return array(
			'me' => bors()->user(),
			'is_subscribed' => $db->select('subscriptions', 'COUNT(*)', array('user_id' => bors()->user_id(), 'topic_id' => $this->id())),
			'authors' => objects_array('balancer_board_user', array('id IN' => $user_ids, 'order' => 'title')),
		);
	}
}

