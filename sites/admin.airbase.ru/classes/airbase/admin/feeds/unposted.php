<?php

class airbase_admin_feeds_unposted extends bors_admin_meta_main
{
	function main_class() { return 'bors_external_feeds_entry'; }
	function main_admin_class() { return 'bors_external_feeds_entry'; }
	function order() { return '-id'; }

	function where()
	{
		return array_merge(parent::where(), array(
			'is_suspended' => false,
			'target_object_id' => 0,
		));
	}

	function on_action_recalculate($data)
	{
		$entry = bors_load_uri($data['target']);
		$entry->recalculate();
		return go(url_remove_params($this->url()));
	}
}
