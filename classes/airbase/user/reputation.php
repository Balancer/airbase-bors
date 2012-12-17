<?php

class airbase_user_reputation extends base_page_db
{
	function class_title_vp() { return ec('запись репутации'); }
	function storage_engine() { return 'bors_storage_mysql'; }

	function db_name() { return 'USERS'; }
	function table_name() { return 'reputation_votes'; }
	function table_fields()
	{
		return array(
			'id',
			'user_id',
			'voter_id',
			'owner_id' => 'voter_id',
			'create_time' => 'time',
			'comment',
			'refer' => 'uri',
			'target_class_name',
			'target_object_id',
			'folder_class_name',
			'folder_object_id',
			'category_class_name',
			'category_id',
			'score',
			'is_deleted',
		);
	}

	function owner() { return object_load('bors_user', $this->owner_id()); }
	function target_user() { return object_load('bors_user', $this->user_id()); }
	function class_title() { return 'Запись в репутации'; }
	function title() { return ($this->score() > 0 ? '+1' : '-1').' от '.$this->owner()->title().' к '.$this->target_user()->title(); }
	function titled_url() { return $this->title(); }

	function cache_groups_parent() { return "user-{$this->user_id()}-reputation"; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'voter' => 'balancer_board_user(owner_id)',
		));
	}

	function refer_link()
	{
		$ref = $this->refer();
		if(preg_match('/^\w+__\d+$/', $ref))
		{
			$object = object_load($ref);
			if($snip = object_property_args($object, 'snip', array(200)))
				$snip = " <span class=\"snip\">{$snip}</span>";
			else
				$snip = '';

			switch(object_property($object, 'class_name'))
			{
				case 'balancer_board_post':
				case 'forum_post':
					return object_property($object, 'titled_url_in_container') . $snip;
				default:
					return object_property($object, 'titled_url');
			}
		}

		if(preg_match('!post://(\d+)/?!', $ref, $m))
		{
			$object = object_load('balancer_board_post', $m[1]);
			return object_property($object, 'titled_url_in_container');
		}

		if(preg_match('!topic://(\d+)/?!', $ref, $m))
			return object_property(bors_load('balancer_board_topic', $m[1]), 'titled_link');

		$object = object_load($ref);
		if(is_a($object, 'bors_system_go_internal'))
			$object = $object->target();

		if($object)
			return $object->titled_url_in_container();

		return $ref;
	}

	function target()
	{
		if($this->target_class_name()
				&& ($object = bors_load($this->target_class_name(), $this->target_object_id())))
			return $object;

		$ref = $this->refer();
		if(preg_match('/^\w+__\d+$/', $ref))
			return bors_load_uri($ref);


		if(preg_match('!post://(\d+)/?!', $ref, $m))
			return bors_load('balancer_board_post', $m[1]);

		if(preg_match('!topic://(\d+)/?!', $ref, $m))
			return bors_load('balancer_board_topic', $m[1]);

		$object = bors_load_uri($ref);
		if(is_a($object, 'bors_system_go_internal'))
			return $object->target();

		return $object;
	}

	function score_html($append_text = '')
	{
		if($this->score() > 0)
			return "<span style=\"color:green\">+".intval($this->score()).($append_text ? ": $append_text" : '')."</span>";
		else
			return "<span style=\"color:red\">".$this->score().($append_text ? ": $append_text" : '')."</span>";
	}

	function comment_short()
	{
		$html = lcml_bb(truncate($this->comment(), 500));

		$html .= "<script>add_warn('{$this->internal_uri()}', {$this->voter_id()})</script>";
		if($this->refer())
			$html .= "<div style=\"font-size: 6pt; border-top: 1px dotted #888; color: #888;\">// За: {$this->refer_link()}</div>";

		return $html;
	}

	function items_list_table_row_class()
	{
		return array( ($this->score() > 0 ? 'pos' : 'neg' ) . '_reputation');
	}

	function item_list_fields()
	{
		return array(
			'ctime' => 'Дата',
			'user()->reputation_titled_url()' => 'Кому',
			'voter()->titled_link()' => 'От кого',
			'comment_short' => 'Комментарий',
		);
	}
}
