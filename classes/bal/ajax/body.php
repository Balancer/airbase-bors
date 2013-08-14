<?php

class bal_ajax_body extends bors_page
{
	function pre_show()
	{
		if(!bors()->user())
		{
			debug_hidden_log('bots', "Unregistered user try to load AJAX body");
			return NULL;
		}

		if(parent::pre_show() === true)
			return true;

		$object = bors_load_uri($this->id());
		if(!$object)
			return bors_message(ec("Не могу найти объект ").$this->id());

		if(!$object->topic()->forum()->can_read())
			return bors_message(ec("Нет доступа к ").$object);

		echo $object->body();
		return true;
	}
}
