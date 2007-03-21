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
			$this->config = &new Config();
		}
	}

	$GLOBALS['bors'] = &new Bors();

	function class_load($class, $id, $page=1)
	{
		$class = strtolower($class);
		if(empty($GLOBALS['bors_data']['classes'][$class][$id]))
		{
			$class_name = "BorsClass".ucfirst($class);
			require_once("classes/objects/$class_name.php");
			$GLOBALS['bors_data']['classes'][$class][$id] = &new $class_name($id);
		}

		if(!$page)
			$page = 1;

		$GLOBALS['bors_data']['classes'][$class][$id]->set_page($page);

		return $GLOBALS['bors_data']['classes'][$class][$id];
	}
