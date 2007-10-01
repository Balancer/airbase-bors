<? 
	function smarty_function_dropdown($params, &$smarty)
	{
		extract($params);
		
		$obj = $smarty->get_template_vars('current_form_class');
		
		echo "<select";

		foreach(split(' ', 'name size style') as $p)
			if(!empty($$p))
				echo " $p=\"{$$p}\"";

		echo ">\n";

		if(preg_match("!^(\w+)\->(\w+)!", $list, $m))
			$list = class_load($m[1])->$m[2]();
		else
			$list = $obj->$list();

		$current = $obj->$name();
		
		if(!$current && !empty($list['default']))
			$current = $list['default'];
		
		foreach($list as $id => $name)
			if($id !== 'default')
				echo "<option value=\"$id\"".($id == $current ? " selected=\"selected\"" : "").">$name</option>\n";
	
		echo "</select>";
	}
