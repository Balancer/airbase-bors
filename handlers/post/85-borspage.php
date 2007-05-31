<?
	register_handler('!.*!', 'handler_bors_auto');

	function handler_bors_auto($uri, $m)
	{
//		echo "<tt>try show page '$uri'</tt><br/>";
//		$GLOBALS['cms']['cache_disabled'] = true;
		require_once("classes/objects/Bors.php");

		if($ret = handler_bors_auto_do(borsclass_uri_load($uri)))
			return $ret;

		return false;
	}

	function handler_bors_auto_do($obj)
	{
		if(!$obj)
			return false;
	
		if($obj->preShowProcess())
			return true;

		$GLOBALS['bors']->set_main_object($obj);

		if(empty($GLOBALS['main_uri']))
			$GLOBALS['main_uri'] = $obj->uri();
	
	    require_once('funcs/templates/bors.php');
		$content = template_assign_bors_object($obj);

		if(!empty($GLOBALS['cms']['cache_static']))
		{
			$page = $obj->page();
			$sf = &new CacheStaticFile($obj->uri($page));
			$sf->save($content, $obj->modify_time());

			require_once('funcs/navigation/go.php');
			return go($obj->uri($page), true, 0, false);
		}

        $last_modify = gmdate('D, d M Y H:i:s', $obj->modify_time()).' GMT';
   	    @header ('Last-Modified: '.$last_modify);
		
		echo $content;
		return true;
	}
