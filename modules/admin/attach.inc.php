<?
	function module_admin_attach($uri)
	{
		$hts = new DataBaseHTS();
		
		$data = array(
			'attaches' => $hts->pages_with_parent($uri),
		);
	
		include_once("funcs/templates/assign.php");
		return template_assign_data("attach.html", $data);
	}
?>
