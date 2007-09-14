<?php
class_include('def_page');

class page_fs_separate extends def_page
{
	function storage_engine()	{ return 'storage_fs_separate'; }
	function render_engine()	{ return 'render_page'; }
	function body_engine()		{ return 'body_source'; }
}
