<?

	class user_js_warnings extends base_js
	{
		function cacheable_body()
		{
			$user_id = $this->id();
			
			if(!$user_id)
				return "";
				
			$db = &new DataBase('punbb');
			$warn_count = intval($db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $user_id AND time > ".(time()-WARNING_DAYS*86400)));
			if($warn_count > 10)
				$warn_count = 10;

			if($warn_count<1)
				return "";

			$result = "";
			$len = 0;
			if($full_count = intval($warn_count / 2))
			{
				$result .= str_repeat('☠', $full_count);
				$len += $full_count;
			}
			
			if($warn_count % 2) // Один штраф, "половинка".
			{
				$result .= '<small>☠</small>';
				$len++;
			}
		
			if($len < 5)
				$result .= str_repeat('&nbsp;', 5 - $len);
		
			$result = "<a href=\"http://balancer.ru/user/{$user_id}/warnings/\"><tt class=\"warnings\">{$result}</tt></a>";
			
			if($warn_count >= 10)
			{
				$w = $db->get_array("SELECT time FROM warnings WHERE user_id = {$user_id} ORDER BY time DESC LIMIT 10");
				$w = $w[9];
				$result .= ec('<div style="color:red; font-size:6pt;">бан до '.strftime("%d.%m.%Y", $w+WARNING_DAYS*86400).'</div>');
			}
			
			return $result;
		}

		function url() { return "http://balancer.ru/user/".$this->id()."/warnings.js"; }
		
		function cache_static() { return 86400; }
	}
