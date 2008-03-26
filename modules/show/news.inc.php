<?
    function module_show_news($uri, $limit)
    {
//		echo "msn=$uri. limit=$limit<br/>";
		$hts = &new DataBaseHTS();
//		$GLOBALS['log_level']=10;

		$uri = $hts->normalize_uri($uri);

		$records = array();

		include_once("funcs/datetime.php");

		$children = array();
//		$GLOBALS['log_level'] = 10;
		foreach($hts->get_data_array($uri, 'child') as $child)
			if(empty($children->$child))
				$children[$child] = $hts->get_data($child, 'create_time');
		
		for($i=0; $i<3; $i++)
			foreach(array_keys($children) as $child)
				foreach($hts->get_data_array($child, 'child') as $ch2)
					if(empty($children->$ch2))
						$children[$ch2] = $hts->get_data($ch2, 'create_time');
//		$GLOBALS['log_level']=2;					
		arsort($children);

//		print_r($children);
		
		$shown = 0;

		foreach($children as $child => $create_time)
		{
			if(!preg_match("!^$uri\d{4}/\d{1,2}/\d{1,2}.*/$!",$child))
				continue;

			if(++$shown > $limit)
				continue;

			$records[] = array(
					'uri' => $child,
					'title' => $hts->get_data($child, 'title'),
					'description' => lcml($hts->get_data($child, 'description')),
					'date' => news_time($create_time),
					'body' => lcml($hts->get_data($child, 'source')),
				);
		}

	    $width  = @$GLOBALS['module_data']['width'];
	    $height = @$GLOBALS['module_data']['height'];

//	    if(!$width)		$width  = 'auto';
//	    if(!$height)	$height = 'auto';

		if(preg_match('!^\d+!', $width))	$width  .= "px";
		if(preg_match('!^\d+!', $height))	$height .= "px";

		include_once("engines/smarty/assign.php");
		return template_assign_data("news.htm", array(
			'records'=>$records, 
			'width'=>$width, 
			'height'=>$height));
	}
