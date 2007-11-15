<?php

class_include('def_dbpage');

class base_page_db extends def_dbpage
{
//	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }
	function fields_first() { return NULL; }
}
