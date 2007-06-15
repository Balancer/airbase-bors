<?
    function lp_flash($url,$params)
    {
        list($width,$height)=split("x",(isset($params['size'])?$params['size']:"")."x");
        if(!$width)  $width=468;
        if(!$height) $height=351;
        return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=$width height=$height><param name=movie value=$url><param name=play value=true><param name=loop value=true><param name=quality value=high><embed src=$url width=$width height=$height play=true loop=true quality=high></embed></object>";
    }

	function make_enabled_params($params, $names_list)
	{
		$res = array();
		foreach(split(' ', $names_list) as $name)
			if(!empty($params[$name]))
				$res[] = "$name=\"".$params[$name]."\"";
		return join(' ', $res);
	}

	function lp_param($dummy, $params)
	{
		return "<param ".make_enabled_params($params, 'name value')."></param>";
	}

	function lp_embed($inner, $params)
	{
		return "<embed ".make_enabled_params($params, 'src type wmode width height').">".lcml($inner)."</embed>";
	}

	function lp_object($inner, $params)
	{
		return "<object ".make_enabled_params($params, 'width height').">".lcml($inner)."</object>";
	}

	function lp_style($inner, $params)
	{
		return "<style ".make_enabled_params($params, 'type').">$inner</style>";
	}
