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
			'comment',
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
		$stat = bors_find_first('balancer_board_rpg_vote', [
			'*set' => 'COUNT(*) AS total, SUM(IF(score>0,1,0)) AS pos, SUM(IF(score<0,1,0)) AS neg',
			'request_id' => $this->id(),
		]);

		if($stat && $stat->total())
			$stat_html = "<small><span style=\"color:green\">За: {$stat->pos()}</span>, <span style=\"color:red\">против: {$stat->neg()}</span></small>";
		else
			$stat_html = "";

		if($this->comment())
		{
			return '<h3>'.$this->title()."</h3>\n"
				.bors_lcml::lcml(trim($this->comment()))
				.$stat_html;
		}

		$target = $this->target();
		$target_user = $this->target_user();

		$color = '';
		if($this->request_class_name() == 'balancer_board_rpg_requests_warning')
			$color = $this->request_id() > 0 ? ' style="color:red"' : ' style="color:green"';

		$html = $target->titled_link()
			.'<div style="color: #999">'.$target->snip().'</div>'
			."<small$color>".$this->title().'</small><br/>'
			.$stat_html;

		return $this->id().'. '.$html;
	}

	function actions()
	{
		$actions = [];
		$actions[] = "<form action=\"http://forums.balancer.ru/rpg/requests/approve\" method=\"post\">"
			."<input type=\"hidden\" name=\"rid\" value=\"{$this->id()}\">"
			."<input type=\"hidden\" name=\"score\" value=\"1\">"
			."<input type=\"submit\" value=\"Подтвердить («За»)\">"
			."</form>";
		$actions[] = "<form action=\"http://forums.balancer.ru/rpg/requests/approve\" method=\"post\">"
			."<input type=\"hidden\" name=\"rid\" value=\"{$this->id()}\">"
			."<input type=\"hidden\" name=\"score\" value=\"-1\">"
			."<input type=\"submit\" value=\"Отклонить («Против»)\">"
			."</form>";
		$actions[] = "<form action=\"http://forums.balancer.ru/rpg/requests/approve\" method=\"post\">"
			."<input type=\"hidden\" name=\"rid\" value=\"{$this->id()}\">"
			."<input type=\"hidden\" name=\"score\" value=\"0\">"
			."<input type=\"submit\" value=\"Отозвать голос (если был)\">"
			."</form>";
		return join('', $actions);
	}

	function item_list_fields()
	{
		$fields = [
			'info' => 'Запрос',
			'need_score' => 'Необходимо баллов',
			'have_score' => 'Собрано баллов',
		];


		if(!preg_match('/archive/', bors()->request()->url()))
			$fields['actions'] = 'Действия';

		return $fields;
	}
}
