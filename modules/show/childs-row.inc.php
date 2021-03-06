<?php
    function module_show_childs_row($parent_uri)
    {
		$hts = new DataBaseHTS();
		$parent_uri = $hts->normalize_uri($parent_uri);
		$data = array();
		$links = array();

		if(!empty($GLOBALS['module_data']['add']))
			foreach(split(' ',$GLOBALS['module_data']['add']) as $l)
				$links[] = array('uri'=>$hts->normalize_uri($l), 'title'=>$hts->get_data($l, 'nav_name'));

		foreach($hts->dbh->get_array("
				SELECT c.value as uri, t.value as title
				FROM `hts_data_child` c
					LEFT JOIN `hts_data_title` t ON c.value = t.id
				WHERE c.id = '".addslashes($parent_uri)."'", false) as $link)
			$links[] = $link;

		$data['links'] = $links;
	
		include_once("engines/smarty/assign.php");
		return template_assign_data("xfile:".__DIR__."/childs-row.htm", $data);
    }
