<?
	register_handler('!.*!', 'handler_bors_auto');

	function handler_bors_auto($uri, $m)
	{
//		echo "<tt>try show page '$uri'</tt><br/>"; exit();
		
//		$GLOBALS['cms']['cache_disabled'] = true;
		require_once("classes/objects/Bors.php");

		if($ret = handler_bors_auto_do(class_load($uri, NULL, 1, false)))
			return $ret;

		return false;
	}

	function handler_bors_auto_do($obj)
	{
//		echo "Bors class=".get_class($obj); exit();

		if(!$obj)
			return false;

	    header("Status: 200 OK");

		$processed = $obj->preParseProcess($_GET);
		if($processed === true)
			return true;

		if(!empty($_GET['class_name']))
		{
//			print_r($_GET);
			$form = class_load($_GET['class_name'], @$_GET['id']);
//			echo get_class($form);
//			loglevel(10);
//			
			if(method_exists($form, 'preAction'))
			{
				$processed = $form->preAction($_GET);
				if($processed === true)
					return true;
			}

			if(!$form->id())
				$form->new_instance();

//			$processed2 = $form->preParseProcess();
//			if($processed2 === true)
//				return true;
								   			
			if(empty($_GET['subaction']))
				$method = 'onAction';
			else
				$method = 'onAction_'.$_GET['subaction'];

			global $bors;
//			print_r($form); exit();
				
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
//					echo "Set $key to $val<br />";
					if(method_exists($form, $method))
						$form->$method($val, true);
				}

				
				$bors->changed_save();

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
				$GLOBALS['main_uri'] = $obj->url();
			
			$my_user = class_load(config('user_class'), -1);
			if($my_user && $my_user->id())
				def_page::add_template_data('my_user', $my_user);
	
			if($render_engine = $obj->render_engine())
			{
				$re = class_load($render_engine);
				$content = $re->render($obj);
			}
			else
			{
			    require_once('funcs/templates/bors.php');
				$content = template_assign_bors_object($obj);
			}
		}
		else
			$content = $processed;

		if($content === false)
			return false;

		
		$last_modify = gmdate('D, d M Y H:i:s', $obj->modify_time()).' GMT';
		header('Last-Modified: '.$last_modify);
	   
		if((!empty($GLOBALS['cms']['cache_static']) || $obj->cache_static()) && (empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING']=='del'))
		{
			$page = $obj->page();
			$sf = &new CacheStaticFile($obj->url($page));
			$sf->save($content, $obj->modify_time(), $obj->cache_static());

			foreach(split(' ', $obj->cache_groups()) as $group)
				if($group)
				{
					$group = class_load('cache_group', $group);
					$group->register($obj);
				}
				
		    header("X-Bors: static cache maden");
			
//			require_once('funcs/navigation/go.php');
//			return go($obj->url($page), true, 0, false);
		}
		
		echo $content;
		return true;
	}
