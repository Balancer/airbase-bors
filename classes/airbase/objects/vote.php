<?php

class airbase_objects_vote extends bors_object_db
{
	function fields()
	{
		return array('AB_BORS' => array('bors_objects_votes' => array(
			'id', 'create_time', 'target_class_id', 'target_object_id', 'voter_id', 'score',
		)));
	}
}
