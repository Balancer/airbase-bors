<?php

class forum_tools_topic extends base_page
{
	function _class_file() { return __FILE__; }
	function can_be_empty() { return true; }

	function parents() { return array('forum_topic://'.$this->id()); }
	function topic() { return object_load('forum_topic', $this->id()); }

	function title() { 	return ec('Операции над темой'); }
	function template() { return 'forum/common.html'; }

	function access() { return $this; }
	function can_read() { templates_noindex(); return $this->can_action(); }
	function can_action()
	{
		$me = bors()->user();
        if(!$me || !$me->id())
            return false;
	
//		if($me->id() == 10000)
//			return true;
	
//		if(!$me->group()->can_move())
//			return false;
	
		return true;
	}
}
