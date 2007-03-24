<?
	require_once("classes/storage/MySqlStorage.php");

	class Config
	{
		var $_storage;
		function storage() { return $this->_storage; }
		function set_storage($storage) { $this->_storage = $storage; }

		var $_cache_uri;
		function cache_uri() { return $this->_cache_uri; }
		function set_cache_uri($uri) { $this->_cache_uri = $uri; }

		function Config()
		{
			$this->set_storage(new MySqlStorage());
		}
	}
