<?
    function module_show_news($uri)
    {
		$hts = new DataBaseHTS();
//		$GLOBALS['log_level']=10;

		$records = array();

		include_once("funcs/datetime.php");

		$children = $hts->get_data_array($uri, 'child');
		rsort($children);
		foreach($children as $child)
		{
			if(!preg_match("!^$uri\d{4}/\d{1,2}/\d{1,2}/!",$child))
				continue;

			$records[] = array(
					'uri' => $child,
					'title' => $hts->get_data($child, 'title'),
					'description' => lcml($hts->get_data($child, 'description')),
					'date' => news_time($hts->get_data($child, 'create_time')),
					'body' => lcml($hts->get_data($child, 'source')),
				);
		}

	    $width  = @$GLOBALS['module_data']['width'];
	    $height = @$GLOBALS['module_data']['height'];

//	    if(!$width)		$width  = 'auto';
//	    if(!$height)	$height = 'auto';

		if(preg_match('!^\d+!', $width))	$width  .= "px";
		if(preg_match('!^\d+!', $height))	$height .= "px";

		include_once("funcs/templates/assign.php");
		return template_assign_data("xfile:".dirname(__FILE__)."/news.htm", array('records'=>$records, 'width'=>$width, 'height'=>$height));
	}
?>
