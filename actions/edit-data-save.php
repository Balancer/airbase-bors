<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/users.php");
    require_once("funcs/navigation/go.php");
    require_once("actions/recompile.php");

    function actions_edit_data_save($edit_uri)
    {
		foreach($_POST as $var=>$value)
	    	$$var = $value;

//	    echo "Page = $edit_uri, action=$action"; exit();

        $hts = new DataBaseHTS();

    	if(isset($flags) && $flags)
	    $flags=join(",",$flags);

       	check_access($edit_uri,$hts);

        if(empty($copyright)) $copyright=user_data('nick');
			
		if(!empty($create_time_changed))
		{
			$tmp_time = intval(strtotime($create_time));
			if($tmp_time > 31525200 && $tmp_time < 2000000000)
			$create_time = $tmp_time;
		}
		else
		{
			$create_time = $hts->get_data($edit_uri, 'create_time');
		}

   	    if(!$create_time)
			$create_time=time();

       	$description_source = @$description;

        $old_description = $hts->get_data($edit_uri,'description_source');
   	    $old_title       = $hts->get_data($edit_uri,'title');
       	$old_modify_time = $hts->get_data($edit_uri,'modify_time');

//		echo "<xmp>".print_r($_POST, true)."edit_uri=$edit_uri</xmp>";

        $version = $hts->get_data($edit_uri, 'version');

   	    if(!$version)
       	    $version = 1;

        if($old_description != $description_source || $old_title != $title)
   	    {
       	    $hts->append_data($edit_uri,'backup',NULL, 
           	    array(
               	    'type'=>'edit_data',
                    'title'=>$old_title, 
   	                'description_source'=>$old_description,
       	            'modify_time'=>$old_modify_time,
           	        'backup_time'=>time(),
               	    'version' => $version,
                   	'member_id'=>user_data('member_id')));
            $version++;
   	    }

       	if(empty($nav_name))
           	$nav_name = strtolower($title);

        foreach(split(' ','access_level copyright create_time cr_type description description_source flags nav_name split_type template title type version') as $p)
   	        $hts->set_data($edit_uri, $p, !empty($$p) ? $$p : NULL);
		
//		echo "***$type for $edit_uri***"; exit();

        $ch = new Cache();

        foreach(split("\n", $parents) as $p)
   	    {
       	    $p = trim($p);
           	if($p)
            {
   	            switch(substr($p,0,7))
       	        {
           	        case '*clear*':
               	        $hts->remove_nav_link($edit_uri); 
                        $ch->clear_all();
   	                    $p=NULL; 
       	                break;
           	        case 'delete:':
               	    case 'remove:':
                   	    $p = substr($p,7);
                       	$hts->remove_nav_link($p, $edit_uri); 
                        $ch->clear($p);
   	                    $ch->clear($edit_uri);
       	                break;
           	        default:
               	        $hts->nav_link($p, $edit_uri); 
                   	    $ch->clear($p);
                       	$ch->clear($edit_uri);
                        break;
   	            }
       	        if(!$p)
           	        break;
           }
   	    }    

       	$hts->set_data($edit_uri,'modify_time', time());
	
//  	      append_log($edit_uri, 'change_property', $version);

        recompile($edit_uri);

   	    go("$edit_uri?"); // /cgi-bin/tools/compile/compile.cgi?page=
	}
?>
