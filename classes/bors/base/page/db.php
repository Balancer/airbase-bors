<?php
/*
	Класс, хранящий свои данные в отдельных таблицах БД.
	Приведение старого HTS-формата в одну таблицу данных и отдельные таблицы
	массивов данных.

hts__aliases	- будет использоваться таблица bors_uris (uri -> class(id))
autolink -> таблица keywords
keyword -> массив
backup -> продумать wiki :-/
category - категория тикетов.
email - ? - торг?
fax - ? - торг?
height -> для картинок
images_upload -> ?

child - таблица детей

author - массив авторов
copyright - объединить?
flags - массив?

access_level
color
compile_time
create_time
cr_type
description -> description_html
description_source -> description
forum_id -> comments_id

					    hts_data_local_path
						 hts_data_logdir
						  hts_data_marker
						   hts_data_modify_time
						    hts_data_nav_name
							 hts_data_order
							  hts_data_org
							   hts_data_origin_uri
							    hts_data_parent
								 hts_data_phone
								  hts_data_position
								   hts_data_priority
								    hts_data_public_time
									 hts_data_publisher
									  hts_data_referer
									   hts_data_right_column
									    hts_data_site_store
										 hts_data_size
										  hts_data_source
										   hts_data_split_type
										    hts_data_stop_time
											 hts_data_style
											  hts_data_subscribe
											   hts_data_template
											    hts_data_title
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



class_include('def_dbpage');

class base_page_db extends def_dbpage
{
//	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }
}
