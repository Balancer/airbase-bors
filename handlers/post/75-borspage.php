<?
	register_handler('!.*!', 'handler_bors_auto');

	function handler_bors_auto($uri, $m)
	{
//		echo "<tt>try show page '$uri'</tt><br/>";
//		$GLOBALS['cms']['cache_disabled'] = true;
		require_once("classes/objects/Bors.php");

		if($ret = handler_bors_auto_do(class_load($uri,NULL,1,false)))
			return $ret;

		return false;
	}

	function handler_bors_auto_do($obj)
	{
		if(!$obj)
			return false;

//		echo get_class($obj);

		$processed = $obj->preParseProcess();
		if($processed === true)
			return true;

		if(!empty($_GET['class_name']))
		{
			$form = class_load($_GET['class_name'], @$_GET['id']);

//			$processed2 = $form->preParseProcess();
//			if($processed2 === true)
//				return true;
//			exit("x");
									   			
			if(empty($_GET['action']))
				$method = 'onAction';
			else
				$method = 'onAction_'.$_GET['action'];
				
			global $bors;
				
			if(method_exists($form, $method))
			{
				$result = $form->$method($_GET);
				if($result === true)
					return true;
			}
			else
			{
				foreach($_GET as $key => $val)
				{
					$method = "set_$key";
					if(method_exists($form, $method))
						$form->$method($val, true);
				}

				$bors->changed_save();

				foreach($_GET as $key => $val)
				{
					if(!$val || !preg_match("!^file_(\w+)_delete_do$!", $key, $m))
						continue;
						
					$method = "remove_{$m[1]}_file";
					if(method_exists($form, $method))
						$form->$method(true);
				}
				
				if(!empty($_FILES))
				{
					foreach($_FILES as $file => $params)
					{
						$method = "upload_{$file}_file";
						if(method_exists($form, $method))
							$form->$method($params, true);
					}
				}
			}

			$bors->changed_save();

//			print_r($_FILES);
//			phpinfo();
//			exit();

			if(!empty($_GET['go']))
			{
				$_GET['go'] = str_replace('%OBJECT_ID%', $form->id(), $_GET['go']);
				return go($_GET['go']);
			}
		}
		
		$processed = $obj->preShowProcess();
		if($processed === true)
			return true;
	
		if($processed === false)
		{
			$GLOBALS['bors']->set_main_object($obj);

			if(empty($GLOBALS['main_uri']))
				$GLOBALS['main_uri'] = $obj->uri();
	
		    require_once('funcs/templates/bors.php');
			$content = template_assign_bors_object($obj);
		}
		else
			$content = $processed;

		if(!empty($GLOBALS['cms']['cache_static']) || $obj->cache_static())
		{
			$page = $obj->page();
			$sf = &new CacheStaticFile($obj->uri($page));
			$sf->save($content, $obj->modify_time(), $obj->cache_static());

			require_once('funcs/navigation/go.php');
//			exit("stat");
			return go($obj->uri($page).(empty($_SERVER['QUERY_STRING'])?"":"?".$_SERVER['QUERY_STRING']), true, 0, false);
		}

        $last_modify = gmdate('D, d M Y H:i:s', $obj->modify_time()).' GMT';
   	    @header ('Last-Modified: '.$last_modify);
		
		echo $content;
		return true;
	}
