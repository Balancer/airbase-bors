<?
    register_action_handler('edit-save', 'handler_edit_save');

    function handler_edit_save($uri, $action)
	{
//		echo "Test edit_save handler";

		foreach($_POST as $var=>$value)
			$$var = $value;
		
		action_edit_save($uri);

		// Показываем сохранную страницу
		show_page($uri);
		return true;
	}

    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/users.php");
    require_once("funcs/navigation/go.php");
    require_once("actions/recompile.php");

	function action_edit_save($_uri)
	{
		foreach(split(' ', "source title uri") as $p)
			$$p = @$_POST[$p];

		if($_uri != $uri)
		{
			echo "POST uri '$uri' != parameters uri '$_uri'!";
			exit();
		}

		$user = new User();

    	$hts = new DataBaseHTS();
	    $uri = $hts->normalize_uri($uri);

   		if(!empty($_POST['login']) && !empty($_POST['password']))
   		{
			if(!$user->do_login($_POST['login'], $_POST['password']))
				return;
	   	}
        
		// TODO!
        // Потенциальная уязвимость в безопасности - пользователь может создать новую страницу,
   	    // используя права доступа произвольной страницы. На редактирование - не влияет.
        check_access($uri, $hts);

   	    $log_action = 'edit';

   	    $old_source = $hts->get_data($uri,'source');
        $old_modify_time = $hts->get_data($uri,'modify_time');

   	    $hts->clear_data_cache($uri,'source');
       	$hts->clear_data_cache($uri,'modify_time');

        $version = $hts->get_data($uri, 'version');
        
   	    if(!$version)
       	    $version = 0;

        if($old_source != $source)
   	    {
           	$hts->append_data($uri,'backup',NULL, 
       	        array(
                    'type'=>'edit',
   	                'source'=>$old_source, 
       	            'modify_time'=>$old_modify_time,
           	        'backup_time'=>time(),
               	    'version' => $version,
                   	'member_id'=>user_data('member_id')));
            $version++;
   	    }

       	$hts->clear_data_cache($uri,'version');
        $hts->set_data($uri, 'version', $version);

//        $hts->get_data($uri,'source');

		if($title)
 	       $hts->set_data($uri, 'title', $title);
		   
		if(!empty($ref))
 	       $hts->nav_link($ref, $uri);

        $hts->set_data($uri, 'source', $source);
   	    $hts->set_data($uri, 'modify_time', time());

//        append_log($uri, $log_action, $version);

//		exit("$uri, $source");

        recompile($uri);

        go("$uri?");
   	}
?>
