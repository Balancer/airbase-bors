<? 
	function smarty_function_input($params, &$smarty)
	{
		extract($params);
		
		$obj = $smarty->get_template_vars('current_form_class');
		
		$value = $obj->$name();
		
		echo "<input type=\"text\" name=\"$name\" value=\"".addslashes($value)."\" />\n";
	}
