<?
	require_once("../storage/MySqlStorage.php");

	class Config
	{
		var $storage;
	
		function storage()
		{
			return $this->storage;
		}

		function set_storage($storage)
		{
			$this->storage = $storage;
		}

		function Config()
		{
			$this->set_storage(new MySqlStorage());
		}
	}
