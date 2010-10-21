<?
	require_once('Config.php');

	class Bors
	{
		var $config;
		
		function config()
		{
			return $this->config;
		}

		function Bors()
		{
			$this->config = new Config();
		}
	}

	global $bors;
	$bors = new Bors();
