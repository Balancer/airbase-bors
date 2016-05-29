<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
	require_once('funcs/DataBase.php');

	$db = new driver_mysql('CACHE');

	foreach($db->get_array("SELECT file FROM cached_files WHERE expire_time BETWEEN 0 AND ".time()) as $file)
	{
		echo "$file<br />\n";
		$db->query("DELETE FROM cached_files WHERE file = '".addslashes($file)."'");
		@unlink($file);
	}
