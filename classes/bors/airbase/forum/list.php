<?php

class airbase_forum_list extends base_db_list
{
	function item_class() { return 'airbase_forum_forum'; }
	function zero_item() { return ec('Не указан'); }
}
