<?php

class airbase_objects_vote extends base_object_db
{
	function fields()
	{
		return array('BORS' => array('bors_objects_votes' => array(
			'id', 'create_time', 'target_class_id', 'target_object_id', 'voter_id', 'score',
		)));
	}
}