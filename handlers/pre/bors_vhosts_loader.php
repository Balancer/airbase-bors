<?
	global $bors_data;
	$bors_data['vhost_handlers'] = array();

	function register_vhost($host, $documents_root=NULL, $bors_local=NULL)
	{
		global $bors_data;
		
		if(empty($documents_root))
			$documents_root = '/var/www/'.$host.'/htdocs';
		
		if(empty($bors_local))
			$bors_local = dirname($documents_root).'/bors-local';
			
		$map = array();

		if(file_exists($file = BORS_INCLUDE.'/vhosts/'.$host.'/handlers/bors_map.php'))
			include($file);

		if(file_exists($file = $bors_local.'handlers/bors_map.php'))
			include($file);
			
		$bors_data['vhosts'][$host] = array(
			'bors_map' => $map,
			'bors_local' => $bors_local,
		);
	}

	@include_once("config/vhosts.php");
