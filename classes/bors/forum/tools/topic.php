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
	function can_action($action, $data)
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
		$topic = $this->topic();

		foreach(explode(' ', 'description keywords_string title answer_notice admin_notice') as $key)
			call_user_func([$topic, "set_{$key}"], empty($data[$key]) ? NULL : $data[$key], true);

		bors_debug::syslog('topic-edit', "{$topic->debug_title()} edited to " . print_r($data, true));

		balancer_board_action::add($topic, "Редактирование параметров темы");

		$topic->cache_clean();
		return go($topic->url());
	}

	function body_data()
	{
		$db = new driver_mysql(config('punbb.database', 'AB_FORUMS'));

		return array(
			'me' => bors()->user(),
			'is_subscribed' => $db->select('subscriptions', 'COUNT(*)', array('user_id' => bors()->user_id(), 'topic_id' => $this->id())),
		);
	}
}

