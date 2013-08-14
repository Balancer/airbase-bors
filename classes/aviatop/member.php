<?php

class aviatop_member extends bors_page_db
{
	function config_class() { return 'aviatop_config'; }
	function template() { return 'forum/page.html'; }

	function parents() { return array('http://top.airbase.ru/'); }

	function db_name() { return 'AVIATOP'; }
	function table_name() { return 'aviatop_members'; }
	function table_fields()
	{
		return array(
			'id',
			'title',
			'description',
			'source',
			'owner_id' => 'owner',
			'email',
			'site_url_db' => 'url',
			'www_raw' => 'url',
			'logo',
			'started',
			'place',
			'visits',
		);
	}

	function age_days() { return intval($this->age()/86400); }
	function age() { return time() - $this->started(); }

	function www() { return $this->site_url(); }

	function site_url()
	{
		if(!preg_match('!http://!', $this->site_url_db()))
			return 'http://'.$this->site_url_db();

		return $this->site_url_db();
	}

	function auto_objects()
	{
		return array(
			'owner' => 'balancer_board_user(owner_id)',
		);
	}

	function per_week()
	{
		$pw = objects_first('aviatop_week', array('top_id' => $this->id()));
		return $pw->per_week();
	}

	function url() { return 'http://www.airbase.ru/top/'.$this->id().'/'; }
}
