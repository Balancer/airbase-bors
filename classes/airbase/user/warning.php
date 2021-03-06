<?php

class airbase_user_warning extends bors_object_db
{
	function class_title() { return ec('Штраф'); }

	function db_name() { return config('punbb.database'); }
	function table_name() { return 'warnings'; }

	function storage_engine() { return 'bors_storage_mysql'; }

	function replace_on_new_instance() { return true; }

//	function can_delete() { return bors()->user()->is_admin(); }
//	Осторожно! У координаторов должен быть action-доступ
//	function _access_engine_def() { return 'balancer_board_access_balancer'; }

	function table_fields()
	{
		return array(
			'id',
			'user_id',
//			'create_time' => array('name' => 'time', 'comment' => 'Дата выставления'),
			'create_time' => 'time',
			'expire_time' => 'UNIX_TIMESTAMP(`expired_timestamp`)',
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
			'moderator' => 'balancer_board_user(moderator_id)',
			'owner' => 'balancer_board_user(moderator_id)',
			'type' => 'airbase_user_warning_type(type_id)',
			'type_scored' => 'airbase_user_warning_typesco(type_id)',
			'user' => 'balancer_board_user(user_id)',
		));
	}

	function title() { return $this->type_id() ? $this->type_scored() : ''; }
	function description()
	{
		return ec('Штраф <b>').$this->type_scored()
			.($this->source() ? " [{$this->source()}]" : '')
			.'</b>'.ec(' пользователю <i>').$this->user()->title()
			.'</i> '.ec(' от <i>')
			.($this->moderator_id() ? $this->moderator()->title() : 'БалаБОТа')
			.'</i>';
		}

	function referer_titled_link()
	{
		if($this->warn_class_id() > 0)
		{
			$obj = bors_load($this->warn_class_id(), $this->warn_object_id());
			return $obj ? $obj->titled_link() : '?';
		}

		if($this->warn_object_id() && ($obj = bors_load('balancer_board_post', $this->warn_object_id())))
		{
			$has = bors_find_first('airbase_user_warning', array('warn_class_id' => $obj->class_id(), 'warn_object_id' => $obj->id()));
			$this->set_warn_class_id($obj->class_id(), !$has);
			$this->set_warn_object_id($obj->id(), !$has);
			return $obj->titled_link();
		}

		if($obj = bors_load($this->referer()))
		{
			$has = bors_find_first('airbase_user_warning', array('warn_class_id' => $obj->class_id(), 'warn_object_id' => $obj->id()));
			$this->set_warn_class_id($obj->class_id(), !$has);
			$this->set_warn_object_id($obj->id(), !$has);
			return $obj->titled_link();
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

	function set_score($value, $dbup=true) { return $this->set_score_db($value, $dbup); }

	function editor_fields_list()
	{
		return array(
			ec('Тип штрафа:') => 'type_id|dropdown=airbase_user_warning_typesco',
			ec('Комментарий:') => 'source|textarea=4',
			ec('Количество штрафных баллов:') => 'score|int',
		);
	}

	function post_save(&$data)
	{
		$this->user()->_warnings_update();
		return parent::post_save($data);
	}

	function url() { return NULL; }

	function moderator_titled_link()
	{
		if($moderator = $this->moderator())
			return $moderator->titled_link();

		return 'БалаБОТ';
	}

	function user_titled_link()
	{
		if($user = $this->user())
			return $user->titled_link();

		return '???';
	}

	function item_list_fields()
	{
		return array(
			'ctime' => 'Дата выставления',
			'moderator_titled_link' => 'Модератор',
			'user_titled_link' => 'Пользователь',
			'title' => 'Штраф',
			'referer_titled_link' => 'Сообщение',
			'source' => 'Комментарий',
		);
	}

	function items_list_table_row_class()
	{
		$css = array();

		if($this->score() > 0)
			$css[] = 'neg_reputation';
		elseif($this->score() < 0)
			$css[] = 'pos_reputation';

		return $css;
	}

	function __dev()
	{
		$w = bors_load(__CLASS__, 17924);
		echo $w->source();
	}
}
