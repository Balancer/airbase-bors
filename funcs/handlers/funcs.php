<?
	function make_func($func)
	{
		if(function_exists($func))
			return $func;

		if(preg_match("!^DB:(.+)$!", $func, $m))
		{
			list($db, $table, $id, $field) = split(".", $m[1]);
			return create_function('$uri, $m', "\$db = new DataBase('$db'); return \$db->get(\"SELECT $field FROM $table WHERE $id='\".addslashes(\$uri).\"'\");");
		}

		if(preg_match("!array\(.+\)$!", $func, $m))
			return create_function('$uri, $m', "return $func;");

		return create_function('$uri, $m', "return \"".addslashes($func)."\";");
	}
