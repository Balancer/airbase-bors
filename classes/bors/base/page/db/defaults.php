<?php

class_include('base_page_db');

class base_page_db_defaults extends base_page_db
{
	function field_create_time_storage() { return 'create_time(id)'; }
	function field_modify_time_storage() { return 'edit_time(id)'; }
	function field_title_storage() { return 'title(id)'; }
	function field_source_storage() { return 'text(id)'; }
	function field_owner_id_storage() { return 'owner_id(id)'; }

	function owner()
	{
		if($owner_id = $this->owner_id())
			return class_load(config('user_class'), $owner_id);
		else
			return NULL;
	}

//	function template_local_vars() { return parent::template_local_vars().' owner'; }
}
