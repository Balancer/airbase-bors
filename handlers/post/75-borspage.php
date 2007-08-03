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
			if(!$form->id())
				$form->new_instance();

//			$processed2 = $form->preParseProcess();
//			if($processed2 === true)
//				return true;

//			print_r($_GET); exit("x");
									   			
			if(empty($_GET['act']))
				$method = 'onAction';
			else
				$method = 'onAction_'.$_GET['act'];

			global $bors;
//			print_r($form); exit();
				
			if(method_exists($form, $method))
			{
//				exit("Yes!");
				$result = $form->$method($_GET);
				if($result === true)
					return true;
			}
			else
			{
				foreach($_GET as $key => $val)
				{
					$method = "set_$key";
//					echo "Set $key to $val<br />";
					if(method_exists($form, $method))
						$form->$method($val, true);
				}

				
				$bors->changed_save();
				$obj->postSave();

//				print_r($form);

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
			$obj->postSave();

//			print_r($_FILES);
//			phpinfo();
//			exit();

			if(!empty($_GET['go']))
			{
				if($_GET['go'] == "newpage")
					return go($form->url(1));
					
				$_GET['go'] = str_replace('%OBJECT_ID%', $form->id(), $_GET['go']);
				require_once('funcs/navigation/go.php');
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

		if((!empty($GLOBALS['cms']['cache_static']) || $obj->cache_static()) && (empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING']=='del'))
		{
			$page = $obj->page();
			$sf = &new CacheStaticFile($obj->uri($page));
			$sf->save($content, $obj->modify_time(), $obj->cache_static());

			require_once('funcs/navigation/go.php');
//			exit("stat");
			return go($obj->uri($page), true, 0, false);
		}

        $last_modify = gmdate('D, d M Y H:i:s', $obj->modify_time()).' GMT';
   	    @header ('Last-Modified: '.$last_modify);
		
		echo $content;
		return true;
	}
