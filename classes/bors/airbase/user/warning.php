<?php

class airbase_user_warning extends base_object_db
{
	function main_db_storage() { return config('punbb.database', 'punbb'); }
	function main_table_storage() { return 'warnings'; }
	function storage_engine() { return 'bors_storage_mysql'; }

	function db_name() { return 'punbb'; }
	function table_name() { return 'warnings'; }

	function table_fields()
	{
		return array(
			'id',
			'user_id',
//			'create_time' => array('name' => 'time', 'comment' => 'Дата выставления'),
			'create_time' => 'time',
			'expire_time' => array('name' => 'FROM_UNIXTIME(`expired_timestamp`)'),
			'score_db' => 'score',
			'type_id',
			'moderator_id',
			'moderator_name',
			'referer' => 'uri',
			'source' => 'comment',
			'warn_class_id',
			'warn_object_id',
		);
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), array(
			'target' => 'warn_class_id(warn_object_id)',
		));
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'type' => 'airbase_user_warning_type(type_id)',
			'type_scored' => 'airbase_user_warning_typesco(type_id)',
		));
	}

	function title() { return $this->type_scored(); }
	function description() { return ec('Штраф <b>').$this->type_scored().($this->source() ? " [{$this->source()}]" : '').'</b>'.ec(' пользователю <i>').$this->user()->title().'</i> '.ec(' от <i>').$this->moderator()->title().'</i>'; }

	function moderator() { return object_load('balancer_board_user', $this->moderator_id()); }
	function user() { return object_load('balancer_board_user', $this->user_id()); }

	function referer_titled_url()
	{
		if($this->warn_class_id() > 0)
		{
			$obj = object_load($this->warn_class_id(), $this->warn_object_id());
			return $obj ? $obj->titled_url() : '?';
		}

		if($this->warn_object_id() && ($obj = object_load('balancer_board_post', $this->warn_object_id())))
		{
			$has = objects_first('airbase_user_warning', array('warn_class_id' => $obj->class_id(), 'warn_object_id' => $obj->id()));
			$this->set_warn_class_id($obj->class_id(), !$has);
			$this->set_warn_object_id($obj->id(), !$has);
			return $obj->titled_url();
		}

		if($obj = object_load($this->referer()))
		{
			$has = objects_first('airbase_user_warning', array('warn_class_id' => $obj->class_id(), 'warn_object_id' => $obj->id()));
			$this->set_warn_class_id($obj->class_id(), !$has);
			$this->set_warn_object_id($obj->id(), !$has);
			return $obj->titled_url();
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

	function post_save(&$data)
	{
		$this->user()->_warnings_update();
		return parent::post_save($data);
	}
}
