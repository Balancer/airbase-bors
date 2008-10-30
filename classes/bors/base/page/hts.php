<?php
/*
	Класс, хранящий свои данные в отдельных таблицах БД.
	Приведение старого HTS-формата в одну таблицу данных и отдельные таблицы
	массивов данных.
*/

class base_page_hts extends base_page_db
{
	function can_cached() { return false; }
	function storage_engine() { return 'storage_db_mysql_smart'; }
	function config_class() { return config('admin_config_class'); }
	function html_disable() { return false; }
	function lcml_tags_enabled() { return NULL; }

	function loaded() { return $this->title(); }

	function main_db_storage() { return config('mysql_database'); }

	function main_table_storage() { return NULL; }

	function fields_first() { return 'stb_title stb_source stb_description'; }

	function fields()
	{
		return array(
			'HTS' => array(
				'hts_data_source' => array('source' => 'value'),
				'hts_data_title'  => array('title'  => 'value'),
				'hts_data_description'  => array('description'  => 'value'),
				'hts_data_create_time'  => array('create_time'  => 'value'),
				'hts_data_modify_time'  => array('modify_time'  => 'value'),
				'hts_data_nav_name'  => array('nav_name'  => 'value'),
				'hts_data_cr_type'  => array('cr_type'  => 'value'),
				'hts_data_template'  => array('template_db'  => 'value'),
				'hts_data_compile_time'  => array('compile_time'  => 'value'),
			),
		);
	}

	function parents()
	{
//		print_d($this->db->select_array('hts_data_parent', 'value', array('id=' => $this->id())));
		return $this->db->select_array('hts_data_parent', 'value', array('id=' => $this->id()));
	}

	function template()
	{
		if($tpl = $this->template_db())
			return $tpl;

		return parent::template();
	}

	function init()
	{
		$host = $this->args('host');
		if($host && preg_match('!/!', $this->id()))
			$this->set_id('http://'.$host.$this->id());
	
		if(!$this->id())
			$this->set_id($this->called_url());

		return parent::init();
		
//		echo "tpl={$this->template()}<br />";
	}

	function cache_static() { return 3600; }
	function url() { return $this->id(); }

	function admin_url() { return '/admin/?object='.$this->internal_uri(); }
	function edit_url() { return '/admin/edit/?object='.$this->internal_uri(); }

	function editor_fields_list()
	{
		return array(
			ec('Заголовок:') => 'title',
			ec('Тело страницы:') => 'source|textarea=20',
		);
	}

/*
hts__aliases	- будет использоваться таблица bors_uris (uri -> class(id))
autolink -> таблица keywords
keyword -> массив
backup -> продумать wiki :-/
category - категория тикетов.
email - ? - торг?
fax - ? - торг?
height -> для картинок

child - таблица детей

author - массив авторов
copyright - объединить?
flags - массив?

access_level
color
description -> description_html
description_source -> description
forum_id -> comments_id

					    hts_data_local_path
						 hts_data_logdir
						  hts_data_marker
							 hts_data_order
							  hts_data_org
							   hts_data_origin_uri
							    hts_data_parent
								 hts_data_phone
								   hts_data_priority
								    hts_data_public_time
									 hts_data_publisher
									  hts_data_referer
									   hts_data_right_column
										 hts_data_size
										   hts_data_split_type
										    hts_data_stop_time
											 hts_data_style
												 hts_data_type
												  hts_data_version
												   hts_data_views
												    hts_data_views_first
													 hts_data_views_last
													  hts_data_width
													   hts_ext_log
													    hts_ext_referers
														 hts_ext_system_data
														  hts_hosts
														   hts_host_redirect
														    hts_ids
															 hts_keys
															  hts_logs


*/
	
}