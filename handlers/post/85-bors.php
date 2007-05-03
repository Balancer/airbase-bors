<?
	register_handler('!^http://[^/]+/\d{4}/\d{1,2}/\d{1,2}/(\w+)\-(\d+)(,(\d+))?\-\-.+\.html$!', 'handler_bors');

	function handler_bors($uri, $m)
	{
//		echo "<tt>try show page '$uri'</tt><br/>";
		$type = $m[1];
		$id   = $m[2];
		$page = @$m[4];

//		$GLOBALS['cms']['cache_disabled'] = true;

		require_once("classes/objects/Bors.php");
		if(! ($obj = class_load($type, $id, $page)))
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
