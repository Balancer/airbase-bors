<?

	function smarty_template($template_name)
	{
		
		if(preg_match('!^xfile://!', $template_name))
			return $template_name;
		
		if($template_name{0} == '/')
			return "xfile:".$template_name;

		if(file_exists($file = BORS_INCLUDE_LOCAL.'templates/'.$template_name))
			return $file;

		if(file_exists($file = BORS_INCLUDE.'templates/'.$template_name))
			return $file;
		
		return $GLOBALS['cms']['default_template'];
	}
