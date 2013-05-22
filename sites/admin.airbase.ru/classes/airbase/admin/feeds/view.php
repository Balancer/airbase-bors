<?php

class airbase_admin_feeds_view extends bors_admin_meta_main
{
	function main_class() { return 'bors_external_feeds_entry'; }
	function main_admin_class() { return 'bors_external_feeds_entry'; }
	function order() { return '-id'; }
}
