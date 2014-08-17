<?php

class balancer_board_rpg_request extends balancer_board_object_db
{
	var $class_title = 'RPG запрос';
	var $class_title_rp = 'RPG запроса';

	function ignore_on_new_instance() { return true; }

	function table_name() { return 'rpg_requests'; }

	function table_fields()
	{
		return array(
			'id',
			'title',
			'request_class_name',
			'request_id',
			'target_user_id',
			'target_class_name',
			'target_id',
			'request_data',
			'need_score',
			'have_score',
			'create_time' => array('name' => 'UNIX_TIMESTAMP(`create_ts`)'),
			'modify_time' => array('name' => 'UNIX_TIMESTAMP(`modify_ts`)'),
			'owner_id',
			'last_editor_id',
			'last_editor_ip',
			'last_editor_ua',
		);
	}

	// balancer_board_rpg_request::factory('airbase_rpg_request_warning')
	//		->user($user)
	//		->score_mul(3)
	//		->add();

	static function factory($request_class_name)
	{
		return new balancer_board_rpg_helper($request_class_name);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), [
			'target_user' => 'balancer_board_user(target_user_id)',
		]);
	}


	function auto_targets()
	{
		return array_merge(parent::auto_targets(), [
			'target' => 'target_class_name(target_id)',
		]);
	}

	function info()
	{
		$target = $this->target();
		$target_user = $this->target_user();

		$color = '';
		if($this->request_class_name() == 'balancer_board_rpg_requests_warning')
			$color = $this->request_id() > 0 ? ' style="color:red"' : ' style="color:green"';

		$html = $target->titled_link()
			.'<div style="color: #999">'.$target->snip().'</div>'
			."<small$color>".$this->title().'</small>';
		return $html;
	}

	function actions()
	{
		$actions = [];
		$actions[] = "<a href=\"http://forums.balancer.ru/rpg/requests/approve?rid={$this->id()}&score=1\">Подтвердить</a>";
		$actions[] = "<a href=\"http://forums.balancer.ru/rpg/requests/approve?rid={$this->id()}&score=-1\">Отклонить</a>";
		return join('<br/>', $actions);
	}

	function item_list_fields()
	{
		return [
			'info' => 'Запрос',
			'need_score' => 'Необходимо баллов',
			'have_score' => 'Собрано баллов',
			'actions' => 'Действия',
		];
	}
}
