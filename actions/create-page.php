<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/users.php");
    require_once("funcs/navigation/go.php");
    require_once("actions/recompile.php");

	function action_create_page($_uri)
	{

		foreach(split(' ', "source title uri") as $p)
			$$p = @$_POST[$p];

		if(empty($source))
			exit(__FILE__."[".__LINE__."] Нет тела страницы");
		
		if($_uri != $_POST['uri'])
		{
			echo "POST uri '{$_POST['uri']}' != parameters uri '$_uri'!";
			exit();
		}
		
    	foreach(split(' ','description nav_name page ref title uri') as $p)
        	$$p = @$_POST[$p];

    	if(empty($title) && !empty($htitle))
        	$title = $htitle;

		$user = new User();

    	$hts = new DataBaseHTS();
	    $uri = $hts->normalize_uri($uri);

//		echo $hts->normalize_uri($ref);

//		echo "<xmp>$_uri, $title";
//		print_r($_POST);
//		exit();
//		exit($uri);

	    if(empty($ref))
    	    $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_HOST'].$_SERVER['HTTP_REFERER'] : NULL;

	    if(preg_match("!/admin/!", $ref) && user_data('level') < 3)
    	    $ref = ''; //"http://airbase.ru/not_ref_pages/";

   		if(!empty($_POST['login']) && !empty($_POST['password']))
   		{
			if(!$user->do_login($_POST['login'], $_POST['password']))
 				return;
	   	}
        
        // Потенциальная уязвимость в безопасности - пользователь может создать новую страницу,
   	    // используя права доступа произвольной страницы. На редактирование - не влияет.
        check_access($ref, $hts);

        $log_action = 'new_page';

       	$uri = $hts->normalize_uri($uri, true);

        if(empty($title) && empty($uri))
            exit("В БД отсутствует страница '$uri':'$title'!");

   	    if(empty($uri))
       	    exit("Пока не могу сформировать адрес для страницы '$title'");

       if(empty($nav_name))
			$nav_name = strtolower($title);

 	    $hts->set_data($uri,'title',$title);
        $hts->set_data($uri,'nav_name',$nav_name);
        $hts->set_data($uri,'copyright',user_data('nick'));
  	    $hts->set_data($uri,'create_time', time());
        $hts->set_data($uri,'description_source', $description);

        if($ref)
   	        $hts->nav_link($ref, $uri, true);

        $hts->set_data($uri, 'version', 1);

//        $hts->get_data($uri,'source');

        $hts->set_data($uri, 'source', $source);
   	    $hts->set_data($uri, 'modify_time', time());

//        append_log($uri, $log_action, $version);

        recompile($uri);

        go("$uri?"); ///cgi-bin/tools/compile/compile.cgi?page=
	}
?>
