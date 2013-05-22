<?php

class airbase_admin_feeds_main extends bors_admin_meta_main
{
	function main_class() { return 'bors_external_feed'; }
	function order() { return 'id'; }
}
