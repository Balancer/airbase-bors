<?php
    require_once("obsolete/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/users.php");
    require_once("funcs/navigation/go.php");
    require_once("actions/recompile.php");

	function action_create_page($_uri)
	{
//		exit("Try create $_uri");

		foreach(explode(' ', "source title uri") as $p)
			$$p = @$_POST[$p];

		if($_uri != @$_POST['uri'])
		{
			echo "POST uri '{$_POST['uri']}' != parameters uri '$_uri'!";
			exit();
		}
		
    	foreach(explode(' ','description nav_name page referer title uri') as $p)
        	$$p = @$_POST[$p];

		if(sizeof($referer)>1 && $referer{strlen($referer)} == '?')
			$referer = substr($referer, 0, strlen($referer)-1);

    	if(empty($title) && !empty($htitle))
        	$title = $htitle;

		$me = new User();

    	$hts = new DataBaseHTS();
	    $uri = $hts->normalize_uri($uri);

	    if(empty($referer))
    	    $referer = @$_SERVER['HTTP_REFERER'];

	    if(preg_match("!/admin/!", $referer))
    	    $ref = '';

   		if(!empty($_POST['login']) && !empty($_POST['password']))
   		{
			if(!$me->do_login($_POST['login'], $_POST['password']))
 				return;
	   	}
        
        //TODO: Потенциальная уязвимость в безопасности - пользователь может создать новую страницу,
   	    // используя права доступа произвольной страницы. На редактирование - не влияет.
        check_access(empty($new_page) ? $uri : $referer, $hts);

        $log_action = 'new_page';

       	$uri = $hts->normalize_uri($uri, true);

        if(empty($title) && empty($uri))
            exit("В БД отсутствует страница '$uri':'$title'!");

   	    if(empty($uri))
       	    exit("Пока не могу сформировать адрес для страницы '$title'");

       if(empty($nav_name))
			$nav_name = strtolower($title);

 	    $hts->set_data($uri,'title', $title);
        $hts->set_data($uri,'nav_name', $nav_name);
        $hts->set_data($uri,'copyright', $me->get('nick'));
  	    $hts->set_data($uri,'create_time', time());
        $hts->set_data($uri,'description_source', $description);

        if($referer)
   	        $hts->nav_link($referer, $uri, true);

        $hts->set_data($uri, 'version', 1);

        $hts->set_data($uri, 'source', $source);
   	    $hts->set_data($uri, 'modify_time', time());

//        append_log($uri, $log_action, $version);

        recompile($uri);

        go("$uri?");
	}
