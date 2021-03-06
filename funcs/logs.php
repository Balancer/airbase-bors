<?php

	function log_action($type, $uri, $text = NULL)
	{
		$owner = bors()->user()->id();

		$db = new driver_mysql('HTS');
		$db->query("INSERT INTO `hts_logs` SET
			`type` = '".addslashes($type)."',
			`uri` = '".addslashes($uri)."',
		  	`owner` = '".addslashes($owner)."',
			`ip` = '".addslashes($_SERVER['REMOTE_ADDR'])."',
		  	`proxy_ip` = '".addslashes(@$_SERVER['HTTP_VIA']."; X:".@$_SERVER['HTTP_X_FORWARDED_FOR'])."',
		  	`time` = ".time()."
		  	".($text?", `text` = '".addslashes($text)."'":""));
	}

	function log_session_update()
	{
		$session_max_time = 60*10; // 10 минут

		if(!($owner = bors()->user()))
			return;

		if(!($owner = $owner->id()))
			return;

		if(!is_numeric($owner))
			return;

		$owner = intval($owner);

		$db = new driver_mysql('HTS');
		$query = "
			SELECT `record_id`, `time`
			FROM `hts_logs`
			WHERE `owner` = $owner
				AND `type` LIKE 'session'
				AND `time` + `ivalue` >= ".(time()-$session_max_time)."
			ORDER BY `time` + `ivalue` DESC LIMIT 1";
		$ret = $db->get($query);
		if($ret && is_array($ret))
			$db->query("UPDATE `hts_logs` SET `ivalue` = ".(time()-$ret['time'])." WHERE `record_id` = {$ret['record_id']}");
		else
			$db->query("REPLACE INTO `hts_logs` SET
				 `type` = 'session',
			  	 `owner` = $owner,
				 `ip` = '".addslashes($_SERVER['REMOTE_ADDR'])."',
			  	 `proxy_ip` = '".addslashes(@$_SERVER['HTTP_VIA']."; X:".@$_SERVER['HTTP_X_FORWARDED_FOR'])."',
			  	 `time` = ".time().",
			  	 `ivalue` = 0");
	}
?>