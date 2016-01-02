<?php

//$max = bors_find_first('balancer_board_forum', ['order' => '-id'])->id();
//for($fid = 1; $fid <= $max; $fid++)
//	bors_task::add(['balancer_board_forum', $fid, 'update_counts']);

require '../config.php';

Airbase\Task::do_works();


