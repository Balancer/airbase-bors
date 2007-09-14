<?
	class_include("def_js");

	class user_js_warnings extends def_js
	{
		function cacheable_body()
		{
			$user_id = $this->id();
			
			if($user_id)
			{
				$db = &new DataBase('punbb');
				$warn_count = intval($db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $user_id AND time > ".(time()-30*86400)));
			}
			else
				$warn_count = 0;

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
		
			return "<tt class=\"warnings\">{$result}</tt>";
		}
	}
