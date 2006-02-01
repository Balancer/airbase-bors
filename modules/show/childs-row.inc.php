<?
    function module_show_childs_row($parent_uri)
    {
		$hts = new DataBaseHTS();
		$parent_uri = $hts->normalize_uri($parent_uri);
		$data = array();
		$links = array();

		foreach($hts->dbh->get_array("
				SELECT c.id as uri, t.value as title
				FROM `hts_data_child` c
					LEFT JOIN `hts_data_title` t ON c.value = t.id
				WHERE c.id LIKE '".addslashes($parent_uri)."'", false) as $link)
			$links[] = $link;

		$data['links'] = $links;

		include_once("funcs/templates/assign.php");
		return template_assign_data("xfile:".dirname(__FILE__)."/childs-row.htm", $data);
    }
?>
