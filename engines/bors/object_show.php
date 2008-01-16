<?php

	function bors_object_show($obj)
	{
//		echo "Bors class=".get_class($obj); exit();

		if(!$obj)
			return false;

	    header("Status: 200 OK");

		$processed = $obj->preParseProcess($_GET);
		if($processed === true)
			return true;

		if(!empty($_GET['act']))
		{
			if(!$obj->access()->can_action())
				return bors_message(ec("Извините, Вы не можете производить операции с этим ресурсом (class=".get_class($obj).", access=".get_class($obj->access()).")"));

			if(method_exists($obj, $method = "on_action_{$_GET['act']}"))
			{
				$result = $obj->$method($_GET);
				if($result === true)
					return true;
			}
		}

		if(!empty($_GET['class_name']))
		{
//			print_d($_GET); exit();
			$form = object_load($_GET['class_name'], @$_GET['id']);
//			echo $_GET['class_name']; exit();
//			print_d($form);
//			set_loglevel(9);

			if(method_exists($form, 'preAction'))
			{
				$processed = $form->preAction($_GET);
				if($processed === true)
					return true;
			}

			if(!$form->access()->can_action())
				return bors_message(ec("Извините, Вы не можете производить операции с этим ресурсом (class=".get_class($form).", access=".get_class($form->access()).")"));

			if(!$form->id())
				$form->new_instance(array_merge($_FILES, $_GET));
			
			if(empty($_GET['subaction']))
				$method = 'onAction';
			else
				$method = 'onAction_'.$_GET['subaction'];

			global $bors;
//			print_d($_GET);
				
			if(method_exists($form, $method))
			{
				$result = $form->$method($_GET);
				if($result === true)
					return true;
			}
			else
			{
				if(!$form->set_fields($_GET, true, NULL, true))
					return true;
				
				$form->set_modify_time(time(), true);
				
				$bors->changed_save();

				foreach($_GET as $key => $val)
				{
					if(!$val || !preg_match("!^file_(\w+)_delete_do$!", $key, $m))
						continue;
						
					$method = "remove_{$m[1]}_file";
//					if(method_exists($form, $method))
						$form->$method(true);
				}
				
				if(!empty($_FILES))
				{
					foreach($_FILES as $file => $params)
					{
						$method = "upload_{$file}_file";
//						if(method_exists($form, $method))
							$form->$method($params, true);
					}
				}
			}

			$bors->changed_save();

//			exit("Saved");

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

		$page = $obj->page();
		if($obj->url($page) != $obj->called_url())
			return go($obj->url($page), true);

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
//				echo "Bors class content of ".get_class($obj)." with render engine ". $obj->render_engine() . " = " . $content; exit();
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

		if(!$obj->access()->can_read())
			return bors_message(ec("Извините, у Вас не доступа к этому ресурсу"));
		
		$last_modify = gmdate('D, d M Y H:i:s', $obj->modify_time()).' GMT';
		header('Last-Modified: '.$last_modify);
	   
		if((!empty($GLOBALS['cms']['cache_static']) || $obj->cache_static()) && (empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING']=='del'))
		{
//			echo "url={$obj->url_engine()}<br />";
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
