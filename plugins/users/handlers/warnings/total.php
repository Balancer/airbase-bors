<?
	hts_data_prehandler("warnings/", array(
			'body'		=> 'plugins_users_warn_total_body',
			'title'		=> ec('Все активные штрафы'),
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

	function plugins_users_warn_total_body($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$db = new DataBase('punbb');
		
		$data['active_warnings'] = $db->get_array("SELECT * FROM warnings WHERE time > ".(time()-86400*30)." ORDER BY time DESC");

        include_once("funcs/templates/assign.php");
        return template_assign_data("total.html", $data);
	}
?>
