<?php

class airbase_user_warning extends base_object_db
{
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'warnings'; }

	function fields()
	{
		return array('punbb' => array('warnings' => array(
			'id',
			'user_id',
			'create_time' => 'time',
			'score_db' => 'score',
			'type_id',
			'moderator_id',
			'moderator_name',
			'referer' => 'uri',
			'source' => 'comment',
			'warn_class_id',
			'warn_object_id',
		)));
	}

	function auto_targets()
	{
		return array(
			'target' => 'warn_class_id(warn_object_id)',
		);
	}

	function moderator() { return object_load('forum_user', $this->moderator_id()); }
	function user() { return object_load('forum_user', $this->user_id()); }

	function referer_titled_url()
	{
		if($this->warn_class_id() > 0)
		{
			$obj = object_load($this->warn_class_id(), $this->warn_object_id());
			return $obj ? $obj->titled_url() : '?';
		}

		if($this->warn_class_id() == 0)
		{
			if($obj = object_load($this->referer()))
			{
				$has = objects_first('airbase_user_warning', array('warn_class_id' => $obj->class_id(), 'warn_object_id' => $obj->id()));
				$this->set_warn_class_id($obj->class_id(), !$has);
				$this->set_warn_object_id($obj->id(), !$has);
				return $obj->titled_url();
			}

			$this->set_warn_class_id(-1, true);
		}

		return "<a href=\"{$this->referer()}\">{$this->referer()}</a>";
	}

	function score()
	{
		$score = $this->score_db();
		if($score > 0)
			$score = '+'.$score;

		return $score;
	}

	function set_score($value, $dbup) { return $this->set_score_db($value, $dbup); }

	function editor_fields_list()
	{
		return array(
			ec('Тип штрафа:') => 'type_id|dropdown=airbase_user_warning_typesco',
			ec('Комментарий:') => 'source|textarea=4',
			ec('Количество штрафных баллов:') => 'score',
		);
	}
}
