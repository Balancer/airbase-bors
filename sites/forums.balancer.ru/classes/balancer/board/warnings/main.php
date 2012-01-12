<?php

class balancer_board_warnings_main extends balancer_board_paginated
{
	function main_class() { return 'airbase_user_warning'; }
	function items_per_page() { return 100; }
}
