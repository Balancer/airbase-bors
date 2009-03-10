<?php

class aviatop_member extends base_page_db
{
	function main_db() { return 'top'; }
	function main_table() { return 'aviatop_members'; }
	function main_table_fields()
	{
		return array(
			'id',
			'title',
			'description',
			'source',
			'owner',
			'site_url_db' => 'url',
			'logo',
			'started',
			'place',
			'visits',
		);
	}
	
	function age_days() { return intval($this->age()/86400); }
	function age() { return time() - $this->started(); }
	function site_url()
	{
		if(!preg_match('!http://!', $this->site_url_db()))
			return 'http://'.$this->site_url_db();

		return $this->site_url_db();
	}
}
