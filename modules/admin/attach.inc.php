<?
	function module_admin_attach($uri)
	{
		$hts = new DataBaseHTS();

		foreach($hts->pages_with_parent($uri) as $u)
		{
			$attaches[] = array(
				'uri' => $u,
			);
		}
		
		$data = array(
			'attaches' => $attaches,
		);
	
		include_once("funcs/templates/assign.php");
		return template_assign_data("attach.html", $data);
	}
?>
