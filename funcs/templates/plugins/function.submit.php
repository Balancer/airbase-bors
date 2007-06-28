<? 
	function smarty_function_submit($params, &$smarty)
	{
		extract($params);
		
		echo "<input type=\"submit\" value=\"".addslashes($value)."\"/>";
	}
